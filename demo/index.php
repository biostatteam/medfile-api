<?php

use Lcobucci\JWT\Encoding\ChainedFormatter;
use Lcobucci\JWT\Encoding\JoseEncoder;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Lcobucci\JWT\Token\Builder;

include __DIR__ . '/vendor/autoload.php';

$envArg = isset($argv[1]) ? $argv[1] : '.env';
$envFile = file_exists($envArg) ? $envArg : __DIR__ . '/' . basename($envArg);

if (!file_exists($envFile)) {
    echo "Error: Configuration file '$envFile' not found. Please provide a valid file or copy .env.example to .env and configure it.\n";
    exit(1);
}

$config = parse_ini_file($envFile);

if (!$config) {
    echo "Error: Could not parse $envFile file. Please check its format.\n";
    exit(1);
}

$requiredKeys = ['ISSUER', 'AUDIENCE', 'RELATED_TO', 'LOGIN_URL', 'FHIR_BASE_URL', 'PEM_FILE'];
foreach ($requiredKeys as $key) {
    if (empty($config[$key])) {
        echo "Error: Missing required configuration key '$key' in $envFile file.\n";
        exit(1);
    }
}

$pemFile = $config['PEM_FILE'];
if (!file_exists($pemFile) && file_exists(__DIR__ . '/' . $pemFile)) {
    $pemFile = __DIR__ . '/' . $pemFile;
}

if (!file_exists($pemFile)) {
    echo "Error: Private key file '" . $config['PEM_FILE'] . "' not found.\n";
    exit(1);
}

echo "Configuration loaded successfully from $envFile\n\n";

echo 'Create Integrator token' . "\n";

$integratorToken = new Builder(new JoseEncoder(), ChainedFormatter::default())
    ->issuedBy($config['ISSUER'])
    ->permittedFor($config['AUDIENCE'])
    ->issuedAt(new DateTimeImmutable())
    ->expiresAt(new DateTimeImmutable('+1 hour'))
    ->relatedTo($config['RELATED_TO'])
    ->getToken(new Sha256(), InMemory::plainText(file_get_contents($pemFile)))
    ->toString();

echo $integratorToken . "\n";


echo "\n";

echo 'Get Master token' . "\n";

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, $config['LOGIN_URL']);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');


$headers = [
    'Accept: */*',
    'Authorization: Bearer ' . $integratorToken,
];
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$response = curl_exec($ch);
if (curl_errno($ch)) {
    echo 'Error:' . curl_error($ch);
}
curl_close($ch);

$masterTokenData = json_decode($response, true);
$masterToken = $masterTokenData['access_token'] ?? ($masterTokenData ?? null);

if (!$masterToken || is_array($masterToken)) {
    echo "Could not get master token from response.\n";
    echo "Response: " . $response . "\n";
    exit(1);
}

echo $masterToken . "\n";

echo "\n";

echo 'Search for Practitioners' . "\n";

$ch_practitioner = curl_init();

curl_setopt($ch_practitioner, CURLOPT_URL, $config['FHIR_BASE_URL'] . '/Practitioner/_search?_page=1&_count=20');
curl_setopt($ch_practitioner, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch_practitioner, CURLOPT_POST, 1);


$practitioner_headers = [
    'Accept: application/fhir+json',
    'Authorization: Bearer ' . $masterToken,
];
curl_setopt($ch_practitioner, CURLOPT_HTTPHEADER, $practitioner_headers);

$practitioner_response = curl_exec($ch_practitioner);
$http_code = curl_getinfo($ch_practitioner, CURLINFO_HTTP_CODE);

if (curl_errno($ch_practitioner)) {
    echo 'cURL error: ' . curl_error($ch_practitioner);
} elseif ($http_code >= 400) {
    echo "HTTP error: " . $http_code . "\n";
    echo "Response: " . $practitioner_response . "\n";
} else {
    echo $practitioner_response . "\n";
}
curl_close($ch_practitioner);

echo "\n";

echo 'Search for Locations' . "\n";

$ch_location = curl_init();

curl_setopt($ch_location, CURLOPT_URL, $config['FHIR_BASE_URL'] . '/Location/_search?_page=1&_count=20');
curl_setopt($ch_location, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch_location, CURLOPT_POST, 1);

$location_headers = [
    'Accept: application/fhir+json',
    'Authorization: Bearer ' . $masterToken,
];
curl_setopt($ch_location, CURLOPT_HTTPHEADER, $location_headers);

$location_response = curl_exec($ch_location);
$location_http_code = curl_getinfo($ch_location, CURLINFO_HTTP_CODE);

if (curl_errno($ch_location)) {
    echo 'cURL error: ' . curl_error($ch_location);
} elseif ($location_http_code >= 400) {
    echo "HTTP error: " . $location_http_code . "\n";
    echo "Response: " . $location_response . "\n";
} else {
    echo $location_response . "\n";
}
curl_close($ch_location);

echo "\n";

echo 'Search for Patient by PESEL' . "\n";

$pesel = '90010112345';
$patient_search_url = $config['FHIR_BASE_URL'] . '/Patient/_search?identifier=' . $pesel;

$ch_patient = curl_init();
curl_setopt($ch_patient, CURLOPT_URL, $patient_search_url);
curl_setopt($ch_patient, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch_patient, CURLOPT_POST, 1);

$patient_headers = [
    'Accept: application/fhir+json',
    'Authorization: Bearer ' . $masterToken,
];
curl_setopt($ch_patient, CURLOPT_HTTPHEADER, $patient_headers);

$patient_response = curl_exec($ch_patient);
$patient_http_code = curl_getinfo($ch_patient, CURLINFO_HTTP_CODE);
curl_close($ch_patient);

if ($patient_http_code >= 400) {
    echo "HTTP error: " . $patient_http_code . "\n";
    echo "Response: " . $patient_response . "\n";
} else {
    $patientData = json_decode($patient_response, true);
    
    $patientFound = false;
    if (isset($patientData['entry']) && is_array($patientData['entry'])) {
        foreach ($patientData['entry'] as $entry) {
            if (isset($entry['resource']['resourceType']) && $entry['resource']['resourceType'] === 'Patient') {
                $patientFound = true;
                break;
            }
        }
    }

    if (!$patientFound) {
        echo "Patient with PESEL $pesel not found. Creating patient...\n\n";

        $ch_create_patient = curl_init();
        curl_setopt($ch_create_patient, CURLOPT_URL, $config['FHIR_BASE_URL'] . '/Patient');
        curl_setopt($ch_create_patient, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch_create_patient, CURLOPT_POST, 1);

        $patient_payload = json_encode([
            "resourceType" => "Patient",
            "identifier" => [
                [
                    "system" => "urn:oid:2.16.840.1.113883.3.4424.1.1.616",
                    "value" => $pesel
                ]
            ],
            "name" => [
                [
                    "family" => "Kowalski",
                    "given" => ["Jan"]
                ]
            ],
            "telecom" => [
                [
                    "system" => "phone",
                    "value" => "123456789",
                    "use" => "mobile"
                ]
            ],
            "address" => [
                [
                    "use" => "home",
                    "type" => "physical",
                    "line" => ["Testowa 1"],
                    "city" => "Warszawa",
                    "postalCode" => "00-001",
                    "country" => "PL"
                ]
            ]
        ]);

        curl_setopt($ch_create_patient, CURLOPT_POSTFIELDS, $patient_payload);
        $create_patient_headers = [
            'Accept: application/fhir+json',
            'Content-Type: application/fhir+json',
            'Authorization: Bearer ' . $masterToken,
        ];
        curl_setopt($ch_create_patient, CURLOPT_HTTPHEADER, $create_patient_headers);

        $create_response = curl_exec($ch_create_patient);
        $create_http_code = curl_getinfo($ch_create_patient, CURLINFO_HTTP_CODE);
        curl_close($ch_create_patient);

        if ($create_http_code >= 400) {
            echo "Failed to create patient. HTTP error: " . $create_http_code . "\n";
            echo "Response: " . $create_response . "\n";
        } else {
            echo "Patient created successfully.\n\n";
            echo 'Searching for Patient by PESEL again...' . "\n";

            $ch_patient_again = curl_init();
            curl_setopt($ch_patient_again, CURLOPT_URL, $patient_search_url);
            curl_setopt($ch_patient_again, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch_patient_again, CURLOPT_POST, 1);
            curl_setopt($ch_patient_again, CURLOPT_HTTPHEADER, $patient_headers);

            $patient_response_again = curl_exec($ch_patient_again);
            $patient_http_code_again = curl_getinfo($ch_patient_again, CURLINFO_HTTP_CODE);
            curl_close($ch_patient_again);

            if ($patient_http_code_again >= 400) {
                echo "HTTP error: " . $patient_http_code_again . "\n";
                echo "Response: " . $patient_response_again . "\n";
            } else {
                echo $patient_response_again . "\n";
            }
        }
    } else {
        echo "Patient with PESEL $pesel found:\n";
        echo $patient_response . "\n";
    }
}

echo "\n";

echo 'Search for Patient by PESEL (OID syntax)' . "\n";

$oid_system = '2.16.840.1.113883.3.4424.1.1.616';
$patient_search_oid_url = $config['FHIR_BASE_URL'] . '/Patient/_search?identifier=' . $oid_system . '|' . $pesel;

$ch_patient_oid = curl_init();
curl_setopt($ch_patient_oid, CURLOPT_URL, $patient_search_oid_url);
curl_setopt($ch_patient_oid, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch_patient_oid, CURLOPT_POST, 1);
curl_setopt($ch_patient_oid, CURLOPT_HTTPHEADER, $patient_headers);

$patient_oid_response = curl_exec($ch_patient_oid);
$patient_oid_http_code = curl_getinfo($ch_patient_oid, CURLINFO_HTTP_CODE);
curl_close($ch_patient_oid);

if ($patient_oid_http_code >= 400) {
    echo "HTTP error: " . $patient_oid_http_code . "\n";
    echo "Response: " . $patient_oid_response . "\n";
} else {
    echo $patient_oid_response . "\n";
}

echo "\n";

echo 'Search for Patient by PESEL (urn:oid: syntax)' . "\n";

$urn_oid_system = 'urn:oid:2.16.840.1.113883.3.4424.1.1.616';
$patient_search_urn_oid_url = $config['FHIR_BASE_URL'] . '/Patient/_search?identifier=' . $urn_oid_system . '|' . $pesel;

$ch_patient_urn_oid = curl_init();
curl_setopt($ch_patient_urn_oid, CURLOPT_URL, $patient_search_urn_oid_url);
curl_setopt($ch_patient_urn_oid, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch_patient_urn_oid, CURLOPT_POST, 1);
curl_setopt($ch_patient_urn_oid, CURLOPT_HTTPHEADER, $patient_headers);

$patient_urn_oid_response = curl_exec($ch_patient_urn_oid);
$patient_urn_oid_http_code = curl_getinfo($ch_patient_urn_oid, CURLINFO_HTTP_CODE);
curl_close($ch_patient_urn_oid);

if ($patient_urn_oid_http_code >= 400) {
    echo "HTTP error: " . $patient_urn_oid_http_code . "\n";
    echo "Response: " . $patient_urn_oid_response . "\n";
} else {
    echo $patient_urn_oid_response . "\n";
}

echo "\n";
