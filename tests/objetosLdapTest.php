<?php
declare(strict_types=1);
use PHPUnit\Framework\TestCase;
use App\Modelos\objectosLdap;

final class objetosLdapTest extends TestCase {

    /**
     * @dataProvider proveedorFiltros
     */
    public function testParsearFiltro($filtro, $resultado): void {
        $falsaConexion = new stdClass();
        $ldap = new objectosLdap($falsaConexion);
        $fp = invokeMethod($ldap, 'parserFiltro', $filtro);
        $this->assertEquals($fp, $resultado);
    }

    public function proveedorFiltros(){
        return Array(
            Array(Array('uid', '*'), '(uid=*)'),
            Array(Array('uid', 'usuario'), '(uid=usuario)'),
            Array(Array('uid', 'NOT usuario'), '(!(uid=usuario))')
        );
    }

    public function testParsearItems(){
        $falsaConexion = new stdClass();
        $ldap = new objectosLdap($falsaConexion);
        $fp = invokeMethod($ldap, 'parsearItems', Array('uid', '* AND NOT root AND NOT nobody'));
        $this->assertEquals($fp, '(&(uid=*)(!(uid=root))(!(uid=nobody)))');
    }
    
    public function testCrearFiltro(){
        $contenido = Array(
            'uid' => '* AND NOT root AND NOT nobody',
            'cn' => 'NOT root AND NOT nobody'
        );
        $falsaConexion = new stdClass();
        $ldap = new objectosLdap($falsaConexion);
        $fp = invokeMethod($ldap, 'crearFiltro', Array($contenido));
        $this->assertEquals($fp, '(&(objectClass=)(&(uid=*)(!(uid=root))(!(uid=nobody)))(&(!(cn=root))(!(cn=nobody))))');
    }

}

function invokeMethod(&$object, $methodName, array $parameters = array()) {
    $reflection = new \ReflectionClass(get_class($object));
    $method = $reflection->getMethod($methodName);
    $method->setAccessible(true);

    return $method->invokeArgs($object, $parameters);
}