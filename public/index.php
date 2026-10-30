<?php
require __DIR__ . '/../src/bootstrap.php';
redirigir(usuario_actual() ? '/productos/' : '/login.php');
