<?php

function generateResponse($result, $response_message, $response_code, $data = null, $file = null, $line = null)
{
    // Create an array for the response
    $response = array(
        'result' => $result,
        'response_message' => $response_message,
        'response_code' => $response_code,
    );

    if ($data !== null) {

        if ($result == "true") {
            $response['data'] = $data;
        } else {
            $data['file'] = $file;
            $data['line'] = $line;
            $response['error'] = $data;
        }
    }
    // Set the appropriate HTTP response code (default: 200 OK)
    http_response_code($response_code);

    // Return the response as JSON
    return json_encode($response);
}


function generateDashboardResponse($response_code,  $result,  $title,  $response_message, $action)
{
    // Create an array for the response
    $response = array(
        'ResponseCode' => $response_code,
        'Result' => $result,
        'title' => $title,
        'message' => $response_message,
        'action' => $action,
    );

    // Set the appropriate HTTP response code (default: 200 OK)
    //http_response_code($response_code);

    // Return the response as JSON
    return json_encode($response);
}
function sendMessage($mobiles, $message)
{
    $url = "http://whats-pro.net/backend/public/index.php/api/messages/send";
    $token = "efd2mxoGOPTwtyNl9OuufgcTnrC20ErzUKr2fh3mrwl4uAFRqVaTTY8WNyAf";
    $ccode = "EG";

    // Set up the request headers
    $headers = [
        "token: $token",
        "Accept: application/json",
        "Content-Type: application/json"
    ];

    // Prepare the payload
    $payload = [
        "phones" => $mobiles,
        "message" => $message,
        "country_code" => $ccode
    ];

    // Initialize cURL
    $ch = curl_init($url);

    // Set cURL options
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    try {
        // Execute the request
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        // Check if request was successful
        if ($httpCode >= 200 && $httpCode < 300) {
            return true;
        }

        return false;
    } catch (Exception $e) {
        error_log("Error sending message: " . $e->getMessage());
        return false;
    } finally {
        // Close cURL resource
        curl_close($ch);
    }
}
function generateKeyPair(string $keyDirectory): array
{
    // Create a more robust temporary config
    $tempConfig = tempnam(sys_get_temp_dir(), 'openssl-');
    file_put_contents($tempConfig, <<<EOD
# OpenSSL configuration file
oid_section = new_oids

[ new_oids ]
[ req ]
default_bits = 2048
default_keyfile = privkey.pem
distinguished_name = req_distinguished_name
attributes = req_attributes
x509_extensions = v3_ca

[ req_distinguished_name ]
countryName = Country Name (2 letter code)
countryName_default = EG
stateOrProvinceName = State or Province Name (full name)
stateOrProvinceName_default = Cairo
localityName = Locality Name (eg, city)
localityName_default = Cairo
organizationName = Organization Name (eg, company)
organizationName_default = My Company
commonName = Common Name (eg, YOUR name)
commonName_default = localhost

[ req_attributes ]
[ v3_ca ]
basicConstraints = CA:FALSE
keyUsage = nonRepudiation, digitalSignature, keyEncipherment
EOD);

    // Configure OpenSSL with the temporary config
    $config = [
        "config" => $tempConfig,
        "digest_alg" => "sha256",
        "private_key_bits" => 2048,
        "private_key_type" => OPENSSL_KEYTYPE_RSA,
    ];

    // Set environment variable as fallback
    putenv("OPENSSL_CONF=$tempConfig");

    // Generate key pair
    $res = openssl_pkey_new($config);
    if (!$res) {
        unlink($tempConfig);
        throw new RuntimeException("Key generation failed: " . implode("\n", getOpenSSLErrors()));
    }

    // Export private key
    $privateKey = '';
    $exportSuccess = openssl_pkey_export($res, $privateKey, null, $config);
    unlink($tempConfig); // Clean up temp file regardless of success

    if (!$exportSuccess) {
        throw new RuntimeException("Private key export failed: " . implode("\n", getOpenSSLErrors()));
    }

    // Get public key
    $keyDetails = openssl_pkey_get_details($res);
    if ($keyDetails === false) {
        throw new RuntimeException("Failed to get public key: " . implode("\n", getOpenSSLErrors()));
    }
    $publicKey = $keyDetails['key'];

    // Save to files if directory provided
    $savedFiles = [];
    if ($keyDirectory !== null) {
        $keyDirectory = rtrim($keyDirectory, '/\\');

        // Create directory if it doesn't exist
        if (!file_exists($keyDirectory)) {
            if (!mkdir($keyDirectory, 0700, true)) {
                throw new RuntimeException("Failed to create key directory");
            }
        }

        // Validate directory
        if (!is_writable($keyDirectory)) {
            throw new RuntimeException("Key directory is not writable");
        }

        $privateKeyFile = $keyDirectory . '/private.pem';
        $publicKeyFile = $keyDirectory . '/public.pem';

        // Save private key
        if (file_put_contents($privateKeyFile, $privateKey, LOCK_EX) === false) {
            throw new RuntimeException("Failed to save private key");
        }
        chmod($privateKeyFile, 0600);

        // Save public key
        if (file_put_contents($publicKeyFile, $publicKey, LOCK_EX) === false) {
            unlink($privateKeyFile);
            throw new RuntimeException("Failed to save public key");
        }
        chmod($publicKeyFile, 0644);

        $savedFiles = [
            'private' => $privateKeyFile,
            'public' => $publicKeyFile
        ];
    }

    return [
        'private' => $privateKey,
        'public' => $publicKey,
        'files' => $savedFiles
    ];
}

function getOpenSSLErrors(): array
{
    $errors = [];
    while ($error = openssl_error_string()) {
        $errors[] = $error;
    }
    return $errors;
}
function encryptData(string $data, string $publicKey): string
{
    if (is_file($publicKey)) {
        $publicKey = file_get_contents($publicKey);
    }
    
    if (!openssl_public_encrypt($data, $encrypted, $publicKey , OPENSSL_PKCS1_OAEP_PADDING)) {
        throw new Exception("Encryption failed: " . openssl_error_string());
    }

    return base64_encode($encrypted);
}

function decryptData(string $base64EncodedData, string $privateKey): string|false
{
    if (is_file($privateKey)) {
        $privateKey = file_get_contents($privateKey);
    }
    
    $encryptedData = base64_decode($base64EncodedData);
    if ($encryptedData === false) {
        throw new Exception("Base64 decoding failed.");
    }

    if (!openssl_private_decrypt($encryptedData, $decrypted, $privateKey, OPENSSL_PKCS1_OAEP_PADDING)) {
        throw new Exception("Decryption failed: " . openssl_error_string());
    }
    
    return $decrypted;
}
