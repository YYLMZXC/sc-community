<?php
if(!XModel::Get("isAdmin"))XModel::error("你还没有权限");
XModel::Load("welcome","admin",false);