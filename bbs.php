<?php
$id=intval($urlparams['_p0']);
using("model/Bbsitem");
Bbsitem::View($id);
?>