-- MySQL dump 10.15  Distrib 10.0.19-MariaDB, for Linux (x86_64)
--
-- Host: 10.10.20.56    Database: agenlad
-- ------------------------------------------------------
-- Server version	5.5.43-0+deb7u1-log

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

--
-- Table structure for table `accesos`
--

DROP TABLE IF EXISTS `accesos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `accesos` (
  `user` varchar(50) DEFAULT NULL,
  `ip` varchar(16) DEFAULT NULL,
  `estampa` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `accesos`
--

LOCK TABLES `accesos` WRITE;
/*!40000 ALTER TABLE `accesos` DISABLE KEYS */;
/*!40000 ALTER TABLE `accesos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `configuracion`
--

DROP TABLE IF EXISTS `configuracion`;
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

--
-- Dumping data for table `configuracion`
--

LOCK TABLES `configuracion` WRITE;
/*!40000 ALTER TABLE `configuracion` DISABLE KEYS */;
INSERT INTO `configuracion` VALUES ('salud','salud.gob.sv','Oficinas Administrativas del Ministerio de Salud','a:11:{s:4:\"base\";s:21:\"dc=salud,dc=gob,dc=sv\";s:6:\"puerto\";s:3:\"389\";s:8:\"sambaSID\";s:39:\"S-1-5-21-371878337-141820978-2368272707\";s:8:\"servidor\";s:11:\"10.10.20.49\";s:9:\"grupos_ou\";b:0;s:10:\"base_grupo\";s:31:\"ou=Groups,dc=salud,dc=gob,dc=sv\";s:12:\"base_usuario\";s:30:\"ou=Users,dc=salud,dc=gob,dc=sv\";s:11:\"netbiosName\";s:10:\"DIRECTORIO\";s:11:\"mail_domain\";s:12:\"salud.gob.sv\";s:12:\"admin_zimbra\";s:20:\"adsalud@salud.gob.sv\";s:16:\"dn_administrador\";s:30:\"cn=admin,dc=salud,dc=gob,dc=sv\";}');
/*!40000 ALTER TABLE `configuracion` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `credenciales`
--

DROP TABLE IF EXISTS `credenciales`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `credenciales` (
  `dominio` varchar(30) NOT NULL DEFAULT '',
  `firmas` varchar(50) DEFAULT NULL,
  `firmaz` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`dominio`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `credenciales`
--

LOCK TABLES `credenciales` WRITE;
/*!40000 ALTER TABLE `credenciales` DISABLE KEYS */;
INSERT INTO `credenciales` VALUES ('salud.gob.sv','W8uU7RlljmDlu5ZI9hRkOA==','DB4GzZr2w14=');
/*!40000 ALTER TABLE `credenciales` ENABLE KEYS */;
UNLOCK TABLES;

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
  `fecha_nacimiento` date DEFAULT NULL,
  `modificado` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `datos_administrativos`
--

LOCK TABLES `datos_administrativos` WRITE;
/*!40000 ALTER TABLE `datos_administrativos` DISABLE KEYS */;
INSERT INTO `datos_administrativos` VALUES ('alortiz','¿Esta es mi pregunta?','Definitivamente, esta es mi respuesta',NULL,'1990-02-11','2015-07-07 20:13:13'),('cmartin','Segundo apellido de mi madre','Datas',NULL,'1974-08-12','2015-07-09 22:12:03'),('jarias','FECHA NACIMIENTO','13-06-1953',NULL,NULL,'2015-07-08 20:41:02'),('rnajarro','Primera mascota','pacos',NULL,NULL,'2015-07-08 20:25:01');
/*!40000 ALTER TABLE `datos_administrativos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `intentos`
--

DROP TABLE IF EXISTS `intentos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `intentos` (
  `user` varchar(50) DEFAULT NULL,
  `estampa` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `intentos`
--

LOCK TABLES `intentos` WRITE;
/*!40000 ALTER TABLE `intentos` DISABLE KEYS */;
INSERT INTO `intentos` VALUES ('uaci','2015-07-10 18:01:35');
/*!40000 ALTER TABLE `intentos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rol`
--

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

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` VALUES ('4i6vsl14s992akklh1ehgk3a15','dn|s:40:\"uid=clira,ou=Users,dc=salud,dc=gob,dc=sv\";user|s:5:\"clira\";pswd|s:13:\"Climsp4s_2oo9\";rol|s:5:\"admon\";titulo|s:13:\"Administrador\";dominio|s:12:\"salud.gob.sv\";permisos|a:4:{s:10:\"directorio\";s:22:\"Directorio Telefónico\";s:4:\"main\";s:21:\"Cambio de Contraseña\";s:7:\"usuario\";a:3:{s:8:\"usershow\";s:11:\"Ver Usuario\";s:7:\"usermod\";s:17:\"Modificar Usuario\";s:7:\"useradd\";s:15:\"Agregar Usuario\";}s:13:\"actualizacion\";s:23:\"Actualización de Datos\";}','5gi1md9dsbsy.s68xc00lf44b','10.168.10.57','Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/43.0.2357.132 Safari/537.36',1436821392),('62dsf89hpgg4mo1001hu1311h1','dn|s:42:\"uid=alortiz,ou=Users,dc=salud,dc=gob,dc=sv\";user|s:7:\"alortiz\";pswd|s:9:\"Figaro.12\";rol|s:13:\"admin_general\";titulo|s:20:\"Administrador Global\";dominio|s:12:\"salud.gob.sv\";permisos|a:5:{s:10:\"directorio\";s:22:\"Directorio Telefónico\";s:4:\"main\";s:21:\"Cambio de Contraseña\";s:7:\"usuario\";a:3:{s:8:\"usershow\";s:11:\"Ver Usuario\";s:7:\"usermod\";s:17:\"Modificar Usuario\";s:7:\"useradd\";s:15:\"Agregar Usuario\";}s:13:\"configuracion\";a:2:{s:12:\"confdominios\";s:8:\"Dominios\";s:12:\"confpermisos\";s:8:\"Permisos\";}s:13:\"actualizacion\";s:23:\"Actualización de Datos\";}','2nfgnfru6hogk.fr23vw0gwcpe','192.168.2.20','Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/43.0.2357.130 Safari/537.36',1436824478),('srqdu95q5cnin3it7cbpnvq372','','5gi1md9dsbsy.22jrc9kete5ck','10.10.20.1','Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/43.0.2357.130 Safari/537.36',1436820956);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user` (
  `user` varchar(50) DEFAULT NULL,
  `rol` varchar(50) DEFAULT NULL,
  `dominio` varchar(40) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES ('usuario','usuario','salud.gob.sv'),('default','sin_firma',''),('alortiz','admin_general','salud.gob.sv'),('darriola','admon','salud.gob.sv'),('clira','admon','salud.gob.sv'),('rgutierrezr','admon','salud.gob.sv'),('darriola','admin_general','salud.gob.sv'),('lmulato','admon','salud.gob.sv'),('kasoto','tecnico','salud.gob.sv'),('imelendez','tecnico','salud.gob.sv'),('jsportillo','tecnico','salud.gob.sv'),('rsoriano','tecnico','salud.gob.sv'),('rnajarro','admin_general','salud.gob.sv'),('ramolina','tecnico','salud.gob.sv'),('carodriguez','tecnico','salud.gob.sv'),('ajgonzalez','tecnico','salud.gob.sv');
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2015-07-13 15:58:22
