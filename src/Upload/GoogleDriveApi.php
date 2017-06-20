<?php


namespace Upload;


class GoogleDriveApi
{
    protected $client;
    protected $service;

    public function upload($file)
    {
        if (!$this->client) {
            $this->client = $this->getClient();
            $this->service = new \Google_Service_Drive($this->client);

        }

        $content = file_get_contents($file);

        $fileMetadata = new \Google_Service_Drive_DriveFile(array(
            'name' => basename($file)));
        $file = $this->service->files->create($fileMetadata, array(
            'data' => $content,
            'mimeType' => 'image/jpeg',
            'uploadType' => 'multipart',
            'fields' => 'id'));

        return $file->id;
    }


    /**
     * Returns an authorized API client.
     * @return \Google_Client the authorized client object
     */
    protected function getClient()
    {
        $client = new \Google_Client();

        $client->setApplicationName('Bot Uploader');
        $client->setScopes([\Google_Service_Drive::DRIVE_FILE]);
        $client->setAuthConfig(__DIR__.'/../../client_secret.json');
        $client->setAccessType('offline');


        // Load previously authorized credentials from a file.
        $credentialsPath = $this->expandHomeDirectory('~/.credentials/drive-php-quickstart.json');
        if (file_exists($credentialsPath)) {
            $accessToken = json_decode(file_get_contents($credentialsPath), true);
        } else {
            // Request authorization from the user.
            $authUrl = $client->createAuthUrl();
            printf("Open the following link in your browser:\n%s\n", $authUrl);
            print 'Enter verification code: ';
            $authCode = trim(fgets(STDIN));

            // Exchange authorization code for an access token.
            $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);

            // Store the credentials to disk.
            if (!file_exists(dirname($credentialsPath))) {
                mkdir(dirname($credentialsPath), 0700, true);
            }
            file_put_contents($credentialsPath, json_encode($accessToken));
            printf("Credentials saved to %s\n", $credentialsPath);
        }
        $client->setAccessToken($accessToken);

        // Refresh the token if it's expired.
        if ($client->isAccessTokenExpired()) {
            $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
            file_put_contents($credentialsPath, json_encode($client->getAccessToken()));
        }

        return $client;
    }


    /**
     * Expands the home directory alias '~' to the full path.
     * @param string $path the path to expand.
     * @return string the expanded path.
     */
    protected function expandHomeDirectory($path)
    {
        $homeDirectory = getenv('HOME');
        if (empty($homeDirectory)) {
            $homeDirectory = getenv('HOMEDRIVE').getenv('HOMEPATH');
        }

        return str_replace('~', realpath($homeDirectory), $path);
    }
}