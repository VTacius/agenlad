<?php

/* index.html.twig */
class __TwigTemplate_0299b06b84ed47dd709435c4ce4609cc9fa2c67a0350d0e31c8f3ea6f49781d7 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = $this->env->loadTemplate("layout.html.twig");

        $this->blocks = array(
            'titulo' => array($this, 'block_titulo'),
            'javascript' => array($this, 'block_javascript'),
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
        // line 8
        $context["menu"] = (isset($context["menu"]) ? $context["menu"] : null);
        // line 9
        $context["pagina"] = (isset($context["pagina"]) ? $context["pagina"] : null);
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 2
    public function block_titulo($context, array $blocks = array())
    {
        echo "Cambio de contraseña";
    }

    // line 3
    public function block_javascript($context, array $blocks = array())
    {
        echo " 
  <script src=\"";
        // line 4
        echo twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('activos')->getCallable(), array("control/indexControl.js")), "html", null, true);
        echo "\" type=\"text/javascript\"></script>
";
    }

    // line 11
    public function block_lateral($context, array $blocks = array())
    {
        echo " 
<h2>Cambio de contraseña</h2>
<p>Cambio de contraseña para <b>";
        // line 13
        echo twig_escape_filter($this->env, (isset($context["usuario"]) ? $context["usuario"] : null), "html", null, true);
        echo "</b></p>
";
    }

    // line 16
    public function block_contenido($context, array $blocks = array())
    {
        // line 17
        $this->displayParentBlock("contenido", $context, $blocks);
        echo "
  <div class=\"col-lg-11\">
    <form class=\"form-horizontal\" role=\"form\" method=\"POST\" action=\"control/indexControl.php\" id=\"cambiopass\" name=\"cambiopass\">
        <div class=\"form-group\">
          <label for=\"passchangePrima\" class=\"col-lg-3 control-label\">Contraseña Nueva</label>
            <div class=\"col-lg-8\">
              <input type=\"password\" class=\"form-control\" id=\"passchangeprima\" name=\"passchangeprima\" placeholder=\"Contraseña nueva\" autofocus>
            </div>
        </div>
        <div id=\"pswd_info\">
            <ul class=\"list-unstyled\">
                <li>La contraseña debe cumplir las siguientes caracteristicas</li>
                <li id=\"char\" class=\"invalid\">Contener <strong>un caracter especial: . _ @ & + ! \$ *</strong></li>
                <li id=\"capital\" class=\"invalid\">Contener <strong>una letra mayúscula</strong></li>
                <li id=\"number\" class=\"invalid\">Contener <strong>un numero</strong></li>
                <li id=\"length\" class=\"invalid\">Tener al menos <strong>8 caracteres</strong></li>
            </ul>
        </div>
      <div class=\"form-group\">
        <label for=\"passchangeConfirm\" class=\"col-lg-3 control-label\">Confirmar Contraseña Nueva</label>
            <div class=\"col-lg-8\">
              <input type=\"password\" class=\"form-control\" id=\"passchangeconfirm\" name=\"passchangeconfirm\" placeholder=\"Confirmar Contraseña\">
            </div>
        <div class=\"col-lg-8 alert alert-error\" id=\"advertencia\">
          <ul class=\"list-unstyled\">
                      <li class=\"invalid\"><strong>";
        // line 42
        echo twig_escape_filter($this->env, (isset($context["mensaje"]) ? $context["mensaje"] : null), "html", null, true);
        echo "</strong></li>
          </ul>
            </div>
      </div>
        <div class=\"form-group\">
            <div class=\"col-lg-offset-5 col-lg-12\">
                <button type=\"submit\" class=\"btn btn-primary\" id=\"enviar\" name=\"enviar\">Cambiar Contraseña</button>
            </div>
        </div>
    </form>
  </div>
";
    }

    public function getTemplateName()
    {
        return "index.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  95 => 42,  67 => 17,  64 => 16,  58 => 13,  52 => 11,  46 => 4,  41 => 3,  35 => 2,  30 => 9,  28 => 8,);
    }
}
