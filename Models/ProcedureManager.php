<?php

namespace Wikibots\Models;

class ProcedureManager
{

    public function getProcedures()
    {
        $result = [];
        $pm = new PermissionManager();
        $proceduresDict = IniProcessor::readConfig('Procedures.ini');
        foreach ($proceduresDict as $procedureUrl => $procedureName)
        {
            $result[] = new Procedure($procedureName, $procedureUrl, $pm->getAllowedConfigGroups(IniType::PROCEDURE_CONFIG, $procedureUrl), $pm->getAllowedLogGroups(IniType::PROCEDURE_LOG, $procedureUrl));
        }
        return $result;
    }

    public function getProcedureObject(string $procedureId)
    {
        $pm = new PermissionManager();
        $procedures = IniProcessor::readConfig('Procedures.ini');
        return new Procedure($procedures[$procedureId], $procedureId, $pm->getAllowedConfigGroups(IniType::PROCEDURE_CONFIG, $procedureId), $pm->getAllowedLogGroups(IniType::PROCEDURE_LOG, $procedureId));
    }
}
