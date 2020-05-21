<?php

namespace LaravelSupports\Libraries\Exceptions\Logs;

use LaravelSupports\Libraries\Supports\Objects\ObjectHelper;

/**
 * Exception 에 대한 내용을 기록합니다
 *
 * @author  WilsonParker
 * @class   ExceptionLogger.php
 * @added   2019.03.04
 * @updated 2019.03.04
 * @bug
 * @todo
 * @see
 */
class ExceptionLogger
{
    /**
     * ExceptionRecordable 을 구현하였으며
     * Exception Log 를 기록할  Class 를 저장 합니다
     *
     * @see
     */
    private $recordableClass = ExceptionLogDB::class;
    // private $recordableClass = ExceptionLogFile::class;

    /**
     * $recordable 을 이용하여 Exception 을 기록합니다
     *
     * @param \Exception $exception
     * @return  Void
     * @author  WilsonParker
     * @added   2019.03.04
     * @updated 2019.03.04
     * @bug
     * @see
     */
    public function report($exception)
    {
        try {
            $recordable = ObjectHelper::createInstance($this->recordableClass);
            $exception->err_trace = $this->jTraceEx($exception);
            $recordable->record($exception);
        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }

    /**
     * Exception StackTrace 를 가공합니다
     *
     * @param \Exception $e
     * @param String $seen
     * @return  String
     * @author  WilsonParker
     * @added   2019.03.04
     * @updated 2019.03.04
     * @bug
     * @see
     */
    public function jTraceEx($e, $seen = null)
    {
        $starter = $seen ? 'Caused by: ' : '';
        $result = array();
        if (!$seen) $seen = array();
        $trace = $e->getTrace();
        $prev = $e->getPrevious();
        $result[] = sprintf('%s%s: %s', $starter, get_class($e), $e->getMessage());
        $file = $e->getFile();
        $line = $e->getLine();
        while (true) {
            $current = "$file:$line";
            if (is_array($seen) && in_array($current, $seen)) {
                $result[] = sprintf(' ... %d more', count($trace) + 1);
                break;
            }
            $result[] = sprintf(
                ' at %s%s%s(%s%s%s)',
                count($trace) && array_key_exists('class', $trace[0]) ? str_replace('\\', '.', $trace[0]['class']) : '',
                count($trace) && array_key_exists('class', $trace[0]) && array_key_exists('function', $trace[0]) ? '.' : '',
                count($trace) && array_key_exists('function', $trace[0]) ? str_replace('\\', '.', $trace[0]['function']) : '(main)',
                $line === null ? $file : basename($file),
                $line === null ? '' : ':',
                $line === null ? '' : $line
            );
            if (is_array($seen))
                $seen[] = "$file:$line";
            if (!count($trace))
                break;
            $file = array_key_exists('file', $trace[0]) ? $trace[0]['file'] : 'Unknown Source';
            $line = array_key_exists('file', $trace[0]) && array_key_exists('line', $trace[0]) && $trace[0]['line'] ? $trace[0]['line'] : null;
            array_shift($trace);
        }
        $result = join("\n", $result);
        if ($prev)
            $result .= "\n" . $this->jTraceEx($prev, $seen);

        return $result;
    }
}
