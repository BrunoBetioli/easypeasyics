<?php
namespace BrunoBetioli/EasyPeasyICS;

/* ------------------------------------------------------------------------ */
/* EasyPeasyICS
/* ------------------------------------------------------------------------ */
/* Bruno Betioli, brunobetioli@yahoo.com.br
/* Github: https://gihub.com/brunobetioli
/*
/* Manuel Reinhard, manu@sprain.ch
/* Twitter: @sprain
/* Web: www.sprain.ch
/*
/* Built with inspiration by
/" http://stackoverflow.com/questions/1463480/how-can-i-use-php-to-dynamically-publish-an-ical-file-to-be-read-by-google-calend/1464355#1464355
/* ------------------------------------------------------------------------ */
/* History:
/* 2010/12/17 - Manuel Reinhard - when it all started
/* 2021/09/15 - Bruno Betioli - altered some things from original project
/* ------------------------------------------------------------------------ */

class EasyPeasyICS
{
    protected $calendarName;
    protected $events = [];

    /**
     * Constructor
     * @param string $calendarName
     */
    public function __construct(string $calendarName = null)
    {
        $this->calendarName = $calendarName;
    }

    /**
     * Add event to calendar
     * @param array $mailEvent
     */
    public function addEvent(array $mailEvent)
    {
        $this->events[] = array_merge(array_fill_keys(['start', 'end', 'summary', 'description', 'url', 'organizer', 'organizer_email', 'location'], null), $mailEvent);
    }

    public function render(bool $output = true)
    {
        /* Start Variable */
        $ics = null;

        /* Add header */
        $ics .= 'BEGIN:VCALENDAR';
        $ics .= 'PRODID:-//Mailer//NONSGML v1.0//EN';
        $ics .= 'VERSION:2.0';
        $ics .= 'CALSCALE:GREGORIAN';
        $ics .= 'METHOD:PUBLISH';
        $ics .= 'X-WR-CALNAME:'.$this->calendarName;

        /* Add events */
        foreach ($this->events as $event) {
            $ics .= 'BEGIN:VEVENT';
            $ics .= 'UID:' . md5(uniqid(mt_rand(), true)) . '@EasyPeasyICS.php';
            $ics .= 'DTSTAMP:' . gmdate('Ymd') . 'T' . gmdate('His') . 'Z';
            $ics .= 'DTSTART:' . gmdate('Ymd', $event['start']) . 'T' . gmdate('His', $event['start']) . 'Z';
            $ics .= 'DTEND:' . gmdate('Ymd', $event['end']) . 'T' . gmdate('His', $event['end']) . 'Z';
            if (!empty($event['organizer']) && !empty($event['organizer_email'])) {
                $ics .= 'ORGANIZER;CN=' . $event['organizer'] . ':mailto:' . $event['organizer_email'];
            }
            $ics .= 'SUMMARY:' . str_replace("\n", "\\n", $event['summary']);
            if (!empty($event['description'])) {
                $ics .= 'DESCRIPTION:' . str_replace('\n', '\\n', $event['description']);
            }
            if (!empty($event['location'])) {
                $ics .= 'LOCATION:' . str_replace('\n', '\\n', $event['location']);
            }
            $ics .= 'CREATED:' . gmdate('Ymd') . 'T' . gmdate('His') . 'Z';
            $ics .= 'LAST-MODIFIED:' . gmdate('Ymd') . 'T' . gmdate('His') . 'Z';
            $ics .= 'URL;VALUE=URI:' . $event['url'];
            $ics .= 'END:VEVENT';
        }

        /* Footer */
        $ics .= 'END:VCALENDAR';

        if ($output) {
            /* Output */
            header('Content-type: text/calendar; charset=utf-8');
            header('Content-Disposition: inline; filename='.$this->calendarName.'.ics');
            echo $ics;
        } else {
            return $ics;
        }
    }
}
