<?php

/* directorio.html.twig */
class __TwigTemplate_9ce2e7f4ec8d4dda9cb97588d486cb721d85b9cadeed96629092389dd0a0a372 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = $this->env->loadTemplate("layout.html.twig");

        $this->blocks = array(
            'titulo' => array($this, 'block_titulo'),
            'lateral' => array($this, 'block_lateral'),
            'contenido' => array($this, 'block_contenido'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "layout.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 5
        $context["menu"] = (isset($context["menu"]) ? $context["menu"] : null);
        // line 6
        $context["pagina"] = (isset($context["pagina"]) ? $context["pagina"] : null);
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 2
    public function block_titulo($context, array $blocks = array())
    {
        echo "Listado de usuarios";
    }

    // line 8
    public function block_lateral($context, array $blocks = array())
    {
        // line 9
        echo "
  <div class=\"span12\">
    <h3>Directorio Minsal</h3>
    <p>El siguiente es un directorio de contactos con todos los usuarios del MINSAL<br>
    Se espera que haga un uso correcto del mismo, usuario <b>";
        // line 13
        echo twig_escape_filter($this->env, (isset($context["usuario"]) ? $context["usuario"] : null), "html", null, true);
        echo "</b></p>
</div>

<div class=\"span12\">
    <form class=\"form-horizontal\" role=\"form\" method=\"POST\" id=\"formbusqueda\">
        <div class=\"form-group\">
            <div class=\"col-lg-12\">
                <label for=\"busqueda\" class=\"col-md-12 text-left\">Búsqueda por Nombre o usuario</label>
                <input type=\"text\" class=\"form-control input-x\" id=\"busqueda\" name=\"busqueda\" placeholder=\"Nombre o usuario a buscar\" autofocus autocomplete=\"off\">
            </div>
        </div>
        <div class=\"form-group\">
            <label for=\"establecimiento\" class=\"col-md-12 text-left\">Establecimiento</label>
            <div class=\"col-lg-12\">
                <input type=\"text\" class=\"form-control input-x\" id=\"establecimiento\" name=\"establecimiento\" placeholder=\"Establecimiento\" autocomplete=\"off\">
            </div>
        </div>
        <div class=\"form-group\">
            <label for=\"oficina\" class=\"col-lg-12 text-left\">Oficina</label>
            <div class=\"col-lg-12\">
                <input type=\"text\" class=\"form-control input-x\" id=\"oficina\" name=\"oficina\" placeholder=\"Oficina\" autocomplete=\"off\">
            </div>
        </div>
        <div class=\"form-group\">
            <label id=\"resultado\" class=\"col-lg-12 text-left\"></label>
        </div>
    </form>
</div>
";
    }

    // line 43
    public function block_contenido($context, array $blocks = array())
    {
        // line 44
        $this->displayParentBlock("contenido", $context, $blocks);
        echo "
    <table class=\"table table-condensed\">
        <thead>
            <tr>
                <th>Nombre</th><th>Correo</th><th>Puesto</th><th>Oficina</th>
            </tr>
        </thead>
        <tbody id=respuesta>
        ";
        // line 52
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["usuarios"]) ? $context["usuarios"] : null));
        foreach ($context['_seq'] as $context["_key"] => $context["empleado"]) {
            // line 53
            echo "            <tr>
                <td>";
            // line 54
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["empleado"]) ? $context["empleado"] : null), "cn"), "html", null, true);
            echo "</td><td>";
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["empleado"]) ? $context["empleado"] : null), "mail"), "html", null, true);
            echo "</td><td>";
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["empleado"]) ? $context["empleado"] : null), "title"), "html", null, true);
            echo "</td><td>";
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["empleado"]) ? $context["empleado"] : null), "ou"), "html", null, true);
            echo "</td>
            </tr> 
        ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['empleado'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 57
        echo "        </tbody>
    </table>
";
    }

    public function getTemplateName()
    {
        return "directorio.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  118 => 57,  103 => 54,  100 => 53,  96 => 52,  85 => 44,  82 => 43,  49 => 13,  43 => 9,  40 => 8,  34 => 2,  29 => 6,  27 => 5,);
    }
}
