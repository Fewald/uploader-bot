<?php
namespace Upload;

class FileUploader
{
    /** @var  GoogleDriveApi */
    protected $googleApi;
    /** @var  \Google_Client */
    protected $client;

    /**
     * FileUploader constructor.
     * @param GoogleDriveApi $googleApi
     */
    public function __construct(GoogleDriveApi $googleApi)
    {
        $this->googleApi = $googleApi;
    }


    public function upload($filePath)
    {
        $this->googleApi->upload($filePath);
    }
}