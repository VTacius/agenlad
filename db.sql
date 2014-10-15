
/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
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
  `dominio` varchar(40) DEFAULT NULL,
  `firmas` varchar(50) DEFAULT NULL,
  `bandera_firmas` char(1) DEFAULT NULL,
  `firmaz` varchar(50) DEFAULT NULL,
  `bandera_firmaz` char(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
-- Dumping data for table `user`
LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES ('usuario','usuario','',NULL,NULL,NULL,NULL),('default','sin_firma','',NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

