/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `accesos` (
  `user` varchar(50) DEFAULT NULL,
  `ip` varchar(16) DEFAULT NULL,
  `estampa` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `configuracion` (
  `clave` varchar(50) NOT NULL DEFAULT '',
  `dominio` varchar(40) NOT NULL,
  `descripcion` varchar(100) DEFAULT NULL,
  `attr` text,
  PRIMARY KEY (`clave`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `credenciales` (
  `dominio` varchar(30) DEFAULT NULL,
  `firmas` varchar(50) DEFAULT NULL,
  `firmaz` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`dominio`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `intentos` (
  `user` varchar(50) DEFAULT NULL,
  `estampa` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rol` (
  `rol` varchar(50) DEFAULT NULL,
  `titulo` varchar(50) DEFAULT NULL,
  `permisos` varchar(500) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
-- Dumping data for table `rol`
LOCK TABLES `rol` WRITE;
/*!40000 ALTER TABLE `rol` DISABLE KEYS */;
INSERT INTO `rol` VALUES ('admon','Administrador','a:3:{s:10:\"directorio\";s:22:\"Directorio Telefónico\";s:4:\"main\";s:21:\"Cambio de Contraseña\";s:7:\"usuario\";a:3:{s:8:\"usershow\";s:11:\"Ver Usuario\";s:7:\"usermod\";s:17:\"Modificar Usuario\";s:7:\"useradd\";s:15:\"Agregar Usuario\";}}'),('sin_firma','Invitado (Según parece)','a:2:{s:10:\"directorio\";s:22:\"Directorio Telefónico\";s:4:\"main\";s:21:\"Cambio de Contraseña\";}'),('tecnico','Técnico de Atención','a:3:{s:10:\"directorio\";s:22:\"Directorio Telefónico\";s:4:\"main\";s:21:\"Cambio de Contraseña\";s:8:\"usershow\";s:11:\"Ver Usuario\";}'),('usuario','Usuario','a:2:{s:10:\"directorio\";s:22:\"Directorio Telefónico\";s:4:\"main\";s:21:\"Cambio de Contraseña\";}'),('admin_general','Administrador Global','a:4:{s:10:\"directorio\";s:22:\"Directorio Telefónico\";s:4:\"main\";s:21:\"Cambio de Contraseña\";s:7:\"usuario\";a:3:{s:8:\"usershow\";s:11:\"Ver Usuario\";s:7:\"usermod\";s:17:\"Modificar Usuario\";s:7:\"useradd\";s:15:\"Agregar Usuario\";}s:13:\"configuracion\";a:2:{s:12:\"confdominios\";s:8:\"Dominios\";s:12:\"confpermisos\";s:8:\"Permisos\";}}');
/*!40000 ALTER TABLE `rol` ENABLE KEYS */;
UNLOCK TABLES;

/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sessions` (
  `session_id` varchar(40) NOT NULL DEFAULT '',
  `data` text,
  `csrf` text,
  `ip` varchar(40) DEFAULT NULL,
  `agent` varchar(255) DEFAULT NULL,
  `stamp` int(11) DEFAULT NULL,
  PRIMARY KEY (`session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user` (
  `user` varchar(50) DEFAULT NULL,
  `rol` varchar(50) DEFAULT NULL,
  `dominio` varchar(40) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
INSERT INTO `user` VALUES ('usuario','usuario',''),('default','sin_firma','');

