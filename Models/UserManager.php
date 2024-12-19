<?php

namespace Wikibots\Models;

class UserManager
{
    private bool $userKnown;
    private string $userName;
    private array $userGroups;

    public function __construct(bool $tryAutoLogin = true)
    {
        $this->userKnown = isset($_SESSION['user']);
        if ($this->userKnown) {
            $this->loadUserInfoFromSession();
        }

        if (!$this->userKnown && $tryAutoLogin) {
            if ($this->isUserLoggedInOnWikiSkripta()) {
                $this->loginUserLocally();
            } else {
                $this->userName = 'Nepřihlášen';
                $this->userGroups = ['Žádné'];
            }
        }
    }

    private function isUserLoggedInOnWikiSkripta() : bool
    {
        return (
            isset($_COOKIE['wsdb_session']) ||
            isset($_COOKIE['wsdbUserName']) &&
            isset($_COOKIE['wsdbUserID']) &&
            isset($_COOKIE['wsdbToken'])
        );
    }

    private function loginUserLocally()
    {
        $params = [
            'action' => 'query',
            'format' => 'json',
            'meta' => 'userinfo',
            'uiprop' => 'groupmemberships'
        ];
        $cookies = [
            'wsdbUserName='.$_COOKIE['wsdbUserName'],
            'wsdbUserID='.$_COOKIE['wsdbUserID'],
            'wsdb_session='.$_COOKIE['wsdb_session']
        ];

        $url = Settings::WS_API . '?' . http_build_query($params);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_COOKIE, implode(';', $cookies));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        $result = curl_exec($ch);
        curl_close($ch);

        $jsonData = json_decode($result, true);
        $userGroups = [];
        if (isset($jsonData['query']['userinfo']['groupmemberships']) && isset($jsonData['query']['userinfo']['name'])) {
            $this->userName = $jsonData['query']['userinfo']['name'];
            foreach ($jsonData['query']['userinfo']['groupmemberships'] as $group) {
                $this->userGroups[] = $group['group'];
            }
            $_SESSION['user']['userName'] = $this->userName;
            $_SESSION['user']['userGroups'] = $this->userGroups;
            $this->userKnown = true;
        }
    }

    private function loadUserInfoFromSession() : void
    {
        $this->userName = $_SESSION['user']['userName'];
        $this->userGroups = $_SESSION['user']['userGroups'];
    }

    public function isUserLoggedIn(): bool
    {
        return $this->userKnown;
    }

    public function getUserName(): string
    {
        return $this->userName;
    }

    public function getUserGroups(): array
    {
        return $this->userGroups;
    }

    public function checkUserGroup(UserGroup $groupToCheck)
    {
        return $this->userKnown && in_array($groupToCheck->value, $this->userGroups);
    }
}

