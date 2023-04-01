<?php

namespace App\Modules\Docx\Controllers;


use App\Http\Controllers\MyController;
use App\Modules\Docx\DocumentParser;
use Illuminate\Http\Request;

class ParseDocxController extends MyController
{
    public function __construct()
    {
        $this->middleware('only_moderator');
    }

    private $protokol;

    public function index(Request $request)
    {
        $this->protokol = [
            'partition' => [],
            'number' => '',
            'region' => '',
            'president' => '',
            'secretary' => '',
            'composition' => '',
            'date' => ''
        ];
        $html_array = $this->fileToData($request);

        $partition = [];
        $p_number = 1;
        $i = 1;
        $i_noempty = 1;
        foreach ($html_array as $key => $value) {
            $item = trim($value);
            // если не пустая строка
            if (!empty($item)) {
                if ($i_noempty == 2) {
                    // вытаскиваем дату из 2 строки
                    $this->protokol['date'] = $value;
                }
                $i_noempty++;
                // получаем номер протокола
                $this->parseNumber($item);
                // получаем шапку протокола
                $this->parseHeader($item);
                // получаем разделы
                if ($this->parsePartitionIndex($item)) {
                    if (isset($partition['number'])) {
                        $this->protokol['partition'][] = $partition;
                        $partition = [
                            'speaker' => '',
                            'text' => '',
                            'tasks' => []
                        ];
                        $i = 1;
                    }
                    $partition['number'] = $p_number++;
                    $partition['tasks'] = [];
                    $l = 1;
                    // пропускаем пустые строки
                    while (empty(trim($html_array[$key - $l]))) {
                        $l++;
                    }
                    // получаем докладчика
                    $partition['speaker'] = $this->parseValue($html_array[$key - $l]);
                    $l++;
                    // получем тему доклада
                    $partition['text'] = $html_array[$key - $l];
                } else {
                    if (isset($partition['number'])) {
                        $task = $this->parseTask($item);
//                        dump($partition);
                        if ($task || count($partition['tasks']) == 0) {
                            $task['number'] = $i++;
                            $task['users'] = [];
                            $partition['tasks'][] = $task;
                        } else {
                            $last_key = count($partition['tasks']) - 1;
                            $text = $partition['tasks'][$last_key]['text'];
                            $text .= "\n" . $item;
                            $partition['tasks'][$last_key]['text'] = $text;
                        }
                    }
                }
            } else {
                if (isset($partition['number']) && count($partition['tasks']) > 0) {
                    $this->protokol['partition'][] = $partition;
                    $partition = [
                        'speaker' => '',
                        'text' => '',
                        'tasks' => []
                    ];
                    $i = 1;
                }
            }
        }

        return $this->protokol;
    }

    /**
     * преобразуем фаил в массив сток
     *
     * @param $request
     * @return string[]
     */
    private function fileToData($request)
    {
        $file = $request->file('file');
        $type = $file->getMimeType();
        $tmp_name = tempnam(sys_get_temp_dir(), 'php_docx');
        move_uploaded_file($file->getRealPath(), $tmp_name);
        $html = DocumentParser::parseFromFile($tmp_name, $type);
        $html = str_replace('</p>', '</p>' . PHP_EOL, $html);
        $html = strip_tags($html, '');
        return explode(PHP_EOL, $html);
    }

    private function parseDate($value)
    {
        $ar = explode('.', $value);
        if (count($ar) > 1) {
            $day = strlen($ar[0]) == 1 ? '0' . $ar[0] : $ar[0];
            $month = strlen($ar[1]) == 1 ? '0' . $ar[1] : $ar[1];
            $year = date('Y');
            $now_month = date('m');
            if ($month < $now_month) {
                $year++;
            }
            if (count($ar) > 2) {
                if (strlen($ar[2]) == 2) {
                    $year = substr($year, 0, 2) . $ar[2];
                } else {
                    $year = $ar[2];
                }
            }
            return $year . '-' . $month . '-' . $day;
        }
        return null;
    }

    private function parseTask($value)
    {
        $value = str_replace('–', '-', $value);
        $value = str_replace('—', '-', $value);
        if (substr_count($value, '-') == 0) {
            return false;
        }
        $ar = explode('-', $value);
        $rez = [];
        $rez['executor'] = trim($ar[0]);
        if (count($ar) > 2) {
            $rez['data_ispoln'] = $this->parseDate(trim($ar[1]));
            unset($ar[0]);
            unset($ar[1]);
            $rez['text'] = implode('-', $ar);
        } else {
            $rez['data_ispoln'] = null;
            unset($ar[0]);
            $rez['text'] = implode('-', $ar);
        }


        return $rez;
    }


    private function parseValue($value)
    {
        $tmp = explode(':', $value);
        return trim($tmp[1] ?? '');
    }


    private function parsePartitionIndex($value)
    {
        $txt = mb_strtolower(trim($value));
        if (strpos($txt, 'поставленные задачи') !== false) {
            return true;
        }
        return false;
    }

    /**
     * парсим заголовок
     *
     * @param $value
     * @return void
     */
    private function parseHeader($value)
    {
        $ar = [
            'region' => 'Место проведения',
            'president' => 'Председатель',
            'secretary' => 'Секретарь',
            'composition' => 'Присутствовали'

        ];
        foreach ($ar as $key => $item) {
            if (empty($this->protokol[$key])) {
                $tmp = explode(':', $value);
                if (mb_strtolower(trim($tmp[0])) == mb_strtolower(trim($item))) {
                    $this->protokol[$key] = trim($tmp[1]);
                }
            }
        }
    }

    private function parseNumber($value)
    {
        if (empty($this->protokol['number'])) {
            $this->protokol['title'] = $value;
            $this->protokol['number'] = substr(strrchr($value, ' '), 1);
        }
    }
}
