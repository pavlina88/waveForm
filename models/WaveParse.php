<?php
/**
 * Class WaveParse parses the file for channel.txt.
 */
class WaveParse
{
    /** @var string The parsing file name */
    protected $fileName = '';
    /** @var string */
    protected $fileDir = 'audio';
    /** @var string */
    protected $columnSeparator = '|';
    /** @var bool|int How many rows to parse before stopping */
    protected $limit = false;

    /**
     * Sets the file name of the file to be parsed.
     *
     * @param string $fileName
     *
     * @throws Exception
     */
    public function setFileName(string $fileName)
    {
        $this->fileName = dirname(__FILE__) . DIRECTORY_SEPARATOR . $this->fileDir . DIRECTORY_SEPARATOR . $fileName;

        if (!file_exists($this->fileName)) {
            throw new Exception('Parsing file cannot be found: ' . $this->fileName);
        }
    }


    /**
     * Gets the file data.
     * @return array
     */
    public function getFileData()
    {
        $file = fopen($this->fileName, "r") or die("Unable to open file!");

        $array = [];
        $index = 0;
        while (!feof($file)) {
            $line               = fgets($file);
            $clearSilenceString = strstr(trim($line), ']');
            $prepareFileLine    = str_replace(']', '', $clearSilenceString);

            if (str_starts_with($prepareFileLine, ' silence_end')) {
                $array[$index++][] = trim(strstr($prepareFileLine, '|', true), 'silence_end: ');
                $index++;
            } else {
                $array[$index][] = trim($prepareFileLine, ' silence_start: ');
            }
        }

        fclose($file);
        return $array;
    }

    /**
     * @param $array
     * @return mixed
     */
    public function calculateDuration($array = [])
    {
        $talkDuration = [];
        foreach ($array as $item) {
            $talkDuration[] = [$item[1] - $item[0]];
        }

        return max($talkDuration);
    }

    /**
     * Calculate User Talk in Percentage from the file data.
     * @param $userArray
     * @param $customerArray
     * @return float
     */
    public function calculatePercentage($userArray = [], $customerArray = [])
    {
        $userMonolog = $customerMonolog = 0;
        foreach ($userArray as $item) {
            $userMonolog += $item[1] - $item[0];

        }
        foreach ($customerArray as $item) {
            $customerMonolog += $item[1] - $item[0];
        }
        $allRecord = $userMonolog + $customerMonolog;
        return round(($userMonolog * 100) / $allRecord, 2);
    }
}