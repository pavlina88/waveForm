<?php
include('../audio/customer-channel.txt');
include('../audio/user-channel.txt');
/**
 * Class WaveParse parses the file for channel.txt.
 */
class WaveParse
{
    /** @var string The parsing file name */
    protected $fileName = '';
    /** @var string */
    protected $fileDir = '../audio';
    /**
     * Sets the file name of the file to be parsed.
     *
     * @param string $fileName
     *
     * @throws Exception
     */
    public function setFileName(string $fileName): void
    {
        $this->fileName = dirname(__FILE__) . DIRECTORY_SEPARATOR . $this->fileDir . DIRECTORY_SEPARATOR . $fileName;
    }


    /**
     * Gets the file data.
     * @return array
     */
    public function getFileData(): array
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
     * @param array $array
     *
     * @return mixed
     */
    public function calculateDuration(array $array = []): mixed
    {
        $talkDuration = [];
        foreach ($array as $item) {
            $talkDuration[] = [$item[1] - $item[0]];
        }

        return max($talkDuration);
    }

    /**
     * Calculate User Talk in Percentage from the file data.
     *
     * @param array $userArray
     * @param array $customerArray
     *
     * @return float
     */
    public function calculatePercentage(array $userArray = [], array $customerArray = []): float
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