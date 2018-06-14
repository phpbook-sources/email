<?php namespace PHPBook\Email;

class Attach {
    
    private $fileAlias;

    private $fileBuffer;

    private $filePath;

    public function getFileAlias(): ?String {
        return $this->fileAlias;
    }

    public function setFileAlias(String $fileAlias): Attach {
        $this->fileAlias = $fileAlias;
        return $this;
    }

    public function getFileBuffer(): ?String {
        return $this->fileBuffer;
    }

    public function setFileBuffer(String $fileBuffer): Attach {
        $this->fileBuffer = $fileBuffer;
        return $this;
    }

    public function getFilePath(): ?String {
        return $this->filePath;
    }

    public function setFilePath(String $filePath): Attach {
        $this->filePath = $filePath;
        return $this;
    }
  
}
