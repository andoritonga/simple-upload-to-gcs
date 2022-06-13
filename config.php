<?php

// load GCS library
require_once 'vendor/autoload.php';

use Google\Cloud\Storage\StorageClient;

// Please use your own private key (JSON file content) which was downloaded in step 3 and copy it here
// your private key JSON structure should be similar like dummy value below.
// WARNING: this is only for QUICK TESTING to verify whether private key is valid (working) or not.  
// NOTE: to create private key JSON file: https://console.cloud.google.com/apis/credentials  
$privateKeyFileContent = '{
    "type": "service_account",
    "project_id": "bss-sandbox-env-1",
    "private_key_id": "1830f9f855aed64c7dc4e1c0cbfe9aaa4e7124e2",
    "private_key": "-----BEGIN PRIVATE KEY-----\nMIIEvAIBADANBgkqhkiG9w0BAQEFAASCBKYwggSiAgEAAoIBAQDGhC4goShXwM6p\n/MDff3qhdDiErpBRBfbpDSGkpJjeub2sbfKjqK3md3xW5kEwnkOWG43KccpOuIG9\nf6WL1uynSev4jzyL9422Gorf2AJvp7vUNQ4kXIWFHATOUa6R2ZEg0R7eiO8QwArc\nEsp0pO4fGv/RPjG1qRb23EVHYKkOxPrvPTUzl3beSu3SsqiB8L+3jRDNgsyyjQin\nuz1kKmwRjJZIcfcCfbFGjRt9rE+CCl6N/t/vCj9UW5QppbY7bi44CBj6/jS9HUIa\nRHKad6uAgGEeJBnGxZYESo57udiqEOwNUZAuZa+WEdUum/kY1sTdFrQDA5A8oQIz\n4sDjbgITAgMBAAECggEACvWNHlwHQCDyx8ueUjhVashhvmFssixkHyWMb+xuTA1w\n/S3ssUBEqW57PMjlXGvAS28l+sEhbAsfjniXiQrKccnQ7x3pN8jo197LP+RK7iMS\naXqsVzGMZXbQhRtZSc/7BRbGX36rw/72GoQnDR11z93LtZ97Ww4EqIgsgu5BJwF7\n8ob/mZz4uTFN+aTXqugry1XOVMoOV8ZjvGyLKF/nsAlHi8ZXCvJ9W+LWMbGfKG4N\n+RK6Brm0ZiE7D9p7Me7vK2swyEacHrmBh62IoJF9LB7iLOYBKxh+rmdz0qs37oXy\nTyPOk7pt8D2MsH0MYguZPMudgdmuXQSZ0G738TJgUQKBgQDurUj/0sAxz8QRSxhq\n5iLsyT0abiMBJ7TmUSYNwP2MY3F95kooNggbiOGqFFYduRegKn34+N4w2JB3CnBP\n43Hc1YlgcZo6xd2PI+k7e62oy7A8nycrbnxQcRmzLWkd3WmLfg0WuROw6gntzdSY\nAHPgvw1kM1UoIge+PYcOuSK1rQKBgQDU7LH4e9RqZ63WPGZ+uRvj8pYJJuHokJg0\nQMUMMZr3veMVwvWnXdP0GsXCUf/zqAfxQRDi5X4ONlX/L4PdwLKCv5YOeU0F5m9f\nGhWRink5W8vgTSmA4E49lic3tNb8DlNCMtmmomRqUTb2YDs31kzdlGx9+5u2K6db\nqeKIaB0OvwKBgAJPFteyuD1DH0hzi4btNwVHZRnuWtRWB//6ZP3ZEhrbk2t+YNVG\nwIlTp6s3iyW6dqoL/y/7aFrcnXkpVrDPKHjUcAHI148d/ui1ljoHFkT9w3SyrXRU\nG0vD7FW5aLzrOWwqCgJgQ/qAKRxt01KtHnHcJ0sC3B4JRj3m5nmT/Ov5AoGAA1cD\nI6atmk2uCGJCKVK2AUlY2SPm/0LlYExzytEsYR51ZW39dHagUH/rAjVPEn9cQIiI\nv0ZiR87KYopYQy42np5quCdm8eS/cnpyRCor5J5wAEC1mfPqxkSIsPMBPErtO2iY\nyWhasuA7QHoZ87JE2mTtnrxHoP/mTCXKC+G2MvkCgYB6g3F04wHXlYVqCd35vtej\nBT5GzT0PsJzmJCtbM55rr7Wpj7gtMqHlCc1btsCwRbv6qSUvm63SlP29voAShQqz\naQbo9iYsBrF90b2QRKqPhpDa/vNDNOcOC0AMGUjxSwyRhWRPuYfMmaEFkEVGth9p\nry9XhOGw+BJnDBz435fyAg==\n-----END PRIVATE KEY-----\n",
    "client_email": "bss-object-storage@bss-sandbox-env-1.iam.gserviceaccount.com",
    "client_id": "105418251830132917326",
    "auth_uri": "https://accounts.google.com/o/oauth2/auth",
    "token_uri": "https://oauth2.googleapis.com/token",
    "auth_provider_x509_cert_url": "https://www.googleapis.com/oauth2/v1/certs",
    "client_x509_cert_url": "https://www.googleapis.com/robot/v1/metadata/x509/bss-object-storage%40bss-sandbox-env-1.iam.gserviceaccount.com"
  }';

/*
 * NOTE: if the server is a shared hosting by third party company then private key should not be stored as a file,
 * may be better to encrypt the private key value then store the 'encrypted private key' value as string in database,
 * so every time before use the private key we can get a user-input (from UI) to get password to decrypt it.
 */

function uploadFile($bucketName, $fileContent, $cloudPath) {
    $privateKeyFileContent = $GLOBALS['privateKeyFileContent'];
    // connect to Google Cloud Storage using private key as authentication
    try {
        $storage = new StorageClient([
            'keyFile' => json_decode($privateKeyFileContent, true)
        ]);
    } catch (Exception $e) {
        // maybe invalid private key ?
        print $e;
        return false;
    }

    // set which bucket to work in
    $bucket = $storage->bucket($bucketName);

    // upload/replace file 
    $storageObject = $bucket->upload(
            $fileContent,
            ['name' => $cloudPath]
            // if $cloudPath is existed then will be overwrite without confirmation
            // NOTE: 
            // a. do not put prefix '/', '/' is a separate folder name  !!
            // b. private key MUST have 'storage.objects.delete' permission if want to replace file !
    );

    // is it succeed ?
    return $storageObject != null;
}

function getFileInfo($bucketName, $cloudPath) {
    $privateKeyFileContent = $GLOBALS['privateKeyFileContent'];
    // connect to Google Cloud Storage using private key as authentication
    try {
        $storage = new StorageClient([
            'keyFile' => json_decode($privateKeyFileContent, true)
        ]);
    } catch (Exception $e) {
        // maybe invalid private key ?
        print $e;
        return false;
    }

    // set which bucket to work in
    $bucket = $storage->bucket($bucketName);
    $object = $bucket->object($cloudPath);
    return $object->info();
}
//this (listFiles) method not used in this example but you may use according to your need 
function listFiles($bucket, $directory = null) {

    if ($directory == null) {
        // list all files
        $objects = $bucket->objects();
    } else {
        // list all files within a directory (sub-directory)
        $options = array('prefix' => $directory);
        $objects = $bucket->objects($options);
    }

    foreach ($objects as $object) {
        print $object->name() . PHP_EOL;
        // NOTE: if $object->name() ends with '/' then it is a 'folder'
    }
}