<?php
require 'bd.php';

session_destroy();
redirect('index.php?logout=success');