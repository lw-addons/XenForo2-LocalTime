<?php

namespace LiamW\LocalTime\XF\Entity;

use XF\Mvc\Entity\Structure;
use XF\Util\Time;

class User extends XFCP_User
{
    public function getLocalTime()
    {
        $userTz = new \DateTimeZone($this->timezone);
        $userTime = new \DateTime('now', $userTz);

        $visitorTz = new \DateTimeZone(\XF::visitor()->timezone);
        $visitorTime = new \DateTime('now', $visitorTz);

        $userTimeRelative = new \DateTime($userTime->format('Y-m-d H:i:s'));
        $visitorTimeRelative = new \DateTime($visitorTime->format('Y-m-d H:i:s'));

        $timeInterval = $visitorTimeRelative->diff($userTimeRelative);
        $dateOffset = $userTimeRelative->format('d') - $visitorTimeRelative->format('d');

        $offset = $timeInterval->format('%R%H:%I');

        $dayOffset = '';

        if ($dateOffset == 0) {
            $dayOffset = \XF::phrase('liamw_localtime_today');
        } else if ($dateOffset == -1) {
            $dayOffset = \XF::phrase('liamw_localtime_yesterday');
        } else if ($dateOffset == 1) {
            $dayOffset = \XF::phrase('liamw_localtime_tomorrow');
        }

        return [
            'time' => sprintf('%s, %s', $dayOffset, $userTime->format('H:i')),
            'offset' => \XF::phrase('liamw_localtime_x_plusminus_y', ['localtimezone' => $visitorTime->format('T'), 'offset' => $offset])
        ];
    }

    public static function getStructure(Structure $structure)
    {
        $structure = parent::getStructure($structure);

        $structure->getters['localtime'] = true;

        return $structure;
    }
}