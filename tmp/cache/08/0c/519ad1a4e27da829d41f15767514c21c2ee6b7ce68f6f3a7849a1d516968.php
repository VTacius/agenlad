<?php

/* layout.html.twig */
class __TwigTemplate_080c519ad1a4e27da829d41f15767514c21c2ee6b7ce68f6f3a7849a1d516968 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
            'titulo' => array($this, 'block_titulo'),
            'css' => array($this, 'block_css'),
            'javascript' => array($this, 'block_javascript'),
            'menu' => array($this, 'block_menu'),
            'lateral' => array($this, 'block_lateral'),
            'contenido' => array($this, 'block_contenido'),
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 1
        echo "<html lang=\"es\">
  <head>
    <meta charset=\"utf-8\">
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
    <meta name=\"description\" content=\"Directorio Telefónico en PHP\">
    <meta name=\"author\" content=\"Alexander Ortiz\">
    <link rel=\"shortcut icon\" href=\"favicon.ico\">
    <title>Directorio MINSAL - ";
        // line 8
        $this->displayBlock('titulo', $context, $blocks);
        echo "</title>
    <link href=\"";
        // line 9
        echo twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('activos')->getCallable(), array("css/bootstrap.min.css")), "html", null, true);
        echo "\" rel=\"stylesheet\">
    <link href=\"";
        // line 10
        echo twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('activos')->getCallable(), array("css/jumbotron.css")), "html", null, true);
        echo " \" rel=\"stylesheet\">
    ";
        // line 11
        $this->displayBlock('css', $context, $blocks);
        // line 12
        echo "    <script src=\"";
        echo twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('activos')->getCallable(), array("jquery.min.js")), "html", null, true);
        echo "\" type=\"text/javascript\"></script>
    <script src=\"";
        // line 13
        echo twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('activos')->getCallable(), array("js/bootstrap.min.js")), "html", null, true);
        echo "\" type=\"text/javascript\"></script>
    ";
        // line 14
        $this->displayBlock('javascript', $context, $blocks);
        // line 15
        echo "  </head>
  <body>
    <div class=\"navbar navbar-inverse navbar-fixed-top\">
      <div class=\"container\">
        <div class=\"navbar-header\">
          <button type=\"button\" class=\"navbar-toggle\" data-toggle=\"collapse\" data-target=\".navbar-collapse\">
            <span class=\"icon-bar\"></span>
            <span class=\"icon-bar\"></span>
            <span class=\"icon-bar\"></span>
          </button>
          <a class=\"navbar-brand\" href=\"#\">Directorio MINSAL</a>
        </div>
        <div class=\"navbar-collapse collapse\">
          <ul class=\"nav navbar-nav\">
            ";
        // line 29
        $this->displayBlock('menu', $context, $blocks);
        // line 32
        echo "          </ul>
          <ul class=\"nav navbar-right\">
            <li><a href=\"/login/final\">Cerrar Sesión</a></li>
          </ul>
        </div><!--/.navbar-collapse -->
      </div>
    </div>
    <div class=\"container\">
      <div class=\"col-lg-3\">
      ";
        // line 41
        $this->displayBlock('lateral', $context, $blocks);
        // line 43
        echo "      </div>
        <div class=\"col-lg-9\">
        ";
        // line 45
        $this->displayBlock('contenido', $context, $blocks);
        // line 49
        echo " 
        </div>
    <div class=\"row\">
        <hr>
        <footer class=\"span12\">
        <p class=\"text-center text-info\">DTIC - Unidad de Redes y Seguridad Informática 2014  </p>
      </footer>
    </div>
    </div><!-- /container -->
  </body>
</html>";
    }

    // line 8
    public function block_titulo($context, array $blocks = array())
    {
        echo "  ";
        echo " ";
    }

    // line 11
    public function block_css($context, array $blocks = array())
    {
        echo " ";
    }

    // line 14
    public function block_javascript($context, array $blocks = array())
    {
        echo " ";
    }

    // line 29
    public function block_menu($context, array $blocks = array())
    {
        echo " 
                ";
        // line 30
        $this->env->loadTemplate("menu.html.twig")->display(array_merge($context, array("menu" => (isset($context["menu"]) ? $context["menu"] : null), "pagina" => (isset($context["pagina"]) ? $context["pagina"] : null))));
        // line 31
        echo "            ";
    }

    // line 41
    public function block_lateral($context, array $blocks = array())
    {
        echo "  
      ";
    }

    // line 45
    public function block_contenido($context, array $blocks = array())
    {
        echo " 
        ";
        // line 46
        if ((isset($context["errorLDAP"]) ? $context["errorLDAP"] : null)) {
            // line 47
            echo "            <center><h3>";
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["errorLDAP"]) ? $context["errorLDAP"] : null), "titulo"), "html", null, true);
            echo ": <b>";
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["errorLDAP"]) ? $context["errorLDAP"] : null), "mensaje"), "html", null, true);
            echo "</b></h3></center>
        ";
        }
        // line 49
        echo "        ";
    }

    public function getTemplateName()
    {
        return "layout.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  162 => 49,  154 => 47,  152 => 46,  147 => 45,  140 => 41,  136 => 31,  134 => 30,  129 => 29,  123 => 14,  117 => 11,  110 => 8,  96 => 49,  94 => 45,  90 => 43,  88 => 41,  77 => 32,  75 => 29,  59 => 15,  57 => 14,  53 => 13,  48 => 12,  46 => 11,  42 => 10,  38 => 9,  34 => 8,  25 => 1,);
    }
}
