--
-- Table structure for table `datos_administrativos`
--

DROP TABLE IF EXISTS `datos_administrativos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `datos_administrativos` (
  `usuario` varchar(25) NOT NULL,
  `pregunta` varchar(500) DEFAULT NULL,
  `respuesta` varchar(500) DEFAULT NULL,
  `jvs` varchar(12) DEFAULT NULL,
  `modificado` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

DROP TABLE IF EXISTS `rol`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rol` (
  `rol` varchar(50) DEFAULT NULL,
  `titulo` varchar(50) DEFAULT NULL,
  `permisos` varchar(500) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rol`
--

LOCK TABLES `rol` WRITE;
/*!40000 ALTER TABLE `rol` DISABLE KEYS */;
INSERT INTO `rol` VALUES ('admon','Administrador','a:4:{s:10:\"directorio\";s:22:\"Directorio Telefónico\";s:4:\"main\";s:21:\"Cambio de Contraseña\";s:7:\"usuario\";a:3:{s:8:\"usershow\";s:11:\"Ver Usuario\";s:7:\"usermod\";s:17:\"Modificar Usuario\";s:7:\"useradd\";s:15:\"Agregar Usuario\";}s:13:\"actualizacion\";s:23:\"Actualización de Datos\";}'),('sin_firma','Invitado (Según parece)','a:2:{s:10:\"directorio\";s:22:\"Directorio Telefónico\";s:4:\"main\";s:21:\"Cambio de Contraseña\";}'),('tecnico','Técnico de Atención','a:4:{s:10:\"directorio\";s:22:\"Directorio Telefónico\";s:4:\"main\";s:21:\"Cambio de Contraseña\";s:8:\"usershow\";s:11:\"Ver Usuario\";s:13:\"actualizacion\";s:23:\"Actualización de Datos\";}'),('usuario','Usuario','a:3:{s:10:\"directorio\";s:22:\"Directorio Telefónico\";s:4:\"main\";s:21:\"Cambio de Contraseña\";s:13:\"actualizacion\";s:23:\"Actualización de Datos\";}'),('admin_general','Administrador Global','a:5:{s:10:\"directorio\";s:22:\"Directorio Telefónico\";s:4:\"main\";s:21:\"Cambio de Contraseña\";s:7:\"usuario\";a:3:{s:8:\"usershow\";s:11:\"Ver Usuario\";s:7:\"usermod\";s:17:\"Modificar Usuario\";s:7:\"useradd\";s:15:\"Agregar Usuario\";}s:13:\"configuracion\";a:2:{s:12:\"confdominios\";s:8:\"Dominios\";s:12:\"confpermisos\";s:8:\"Permisos\";}s:13:\"actualizacion\";s:23:\"Actualización de Datos\";}');
/*!40000 ALTER TABLE `rol` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
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


/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2015-07-07 13:11:44
