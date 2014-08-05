<?php

/* login.html.twig */
class __TwigTemplate_b66da6db8fa0589f2f0c9aa46a3e063326ea5763c9edae95940e285c73a5ff33 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 2
        echo "<!DOCTYPE html>
<html lang=\"es\">
  <head>
    <meta charset=\"utf-8\">
    <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\">
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">
    <meta name=\"description\" content=\"Espacio de logueo\">
    <meta name=\"author\" content=\"Alexander Ortíz\">
    <title>Inicio de Sesión</title>
    <link href=\"";
        // line 11
        echo twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('activos')->getCallable(), array("css/bootstrap.min.css")), "html", null, true);
        echo "\" rel=\"stylesheet\">
    <link href=\"";
        // line 12
        echo twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('activos')->getCallable(), array("css/signin.css")), "html", null, true);
        echo "\" rel=\"stylesheet\">
  </head>

  <body>

    <div class=\"container\">
      <form class=\"form-signin\" role=\"form\" id=\"login\" method=\"POST\" action=\"/login/inicio\">
        <h2 class=\"form-signin-heading\">Directorio MINSAL</h2>
        <input name=\"user\" id=\"usuario\" type=\"text\" class=\"form-control\" placeholder=\"Usuario\" required autofocus>
        <input name=\"pswd\" id=\"passwd\" type=\"password\" class=\"form-control\" placeholder=\"Contraseña\" required>
        <button id=\"enviar\" class=\"btn btn-lg btn-primary btn-block\" type=\"submit\">Entrar</button>
        ";
        // line 23
        if ((isset($context["mensaje"]) ? $context["mensaje"] : null)) {
            // line 24
            echo "          <center><label id=\"mensaje\" class=\"checkbox\"><b> ";
            echo twig_escape_filter($this->env, (isset($context["mensaje"]) ? $context["mensaje"] : null), "html", null, true);
            echo " </b></label></center>
        ";
        }
        // line 26
        echo "      </form>

    </div>

  </body>
</html>";
    }

    public function getTemplateName()
    {
        return "login.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  56 => 26,  50 => 24,  48 => 23,  34 => 12,  30 => 11,  19 => 2,);
    }
}
