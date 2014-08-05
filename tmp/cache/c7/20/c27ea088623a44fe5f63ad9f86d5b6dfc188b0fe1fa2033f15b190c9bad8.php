<?php

/* error.html.twig */
class __TwigTemplate_c720c27ea088623a44fe5f63ad9f86d5b6dfc188b0fe1fa2033f15b190c9bad8 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = $this->env->loadTemplate("layout.html.twig");

        $this->blocks = array(
            'titulo' => array($this, 'block_titulo'),
            'javascript' => array($this, 'block_javascript'),
            'lateral' => array($this, 'block_lateral'),
            'menu' => array($this, 'block_menu'),
            'contenido' => array($this, 'block_contenido'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "layout.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
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

    // line 7
    public function block_lateral($context, array $blocks = array())
    {
        echo " 
<h2>Cambio de contraseña</h2>
<p>Cambio de contraseña para <b>";
        // line 9
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["datos"]) ? $context["datos"] : null), "user"), "html", null, true);
        echo "</b></p>
";
    }

    // line 12
    public function block_menu($context, array $blocks = array())
    {
        echo " 
    ";
        // line 13
        $this->env->loadTemplate("menu.html.twig")->display(array_merge($context, array("menu" => (isset($context["menu"]) ? $context["menu"] : null))));
    }

    // line 16
    public function block_contenido($context, array $blocks = array())
    {
        // line 17
        $this->displayParentBlock("contenido", $context, $blocks);
        echo "
  <h2>";
        // line 18
        echo twig_escape_filter($this->env, (isset($context["status"]) ? $context["status"] : null), "html", null, true);
        echo "</h2>
  <h3>";
        // line 19
        echo twig_escape_filter($this->env, (isset($context["title"]) ? $context["title"] : null), "html", null, true);
        echo "</h3>
  <p>";
        // line 20
        echo twig_escape_filter($this->env, (isset($context["text"]) ? $context["text"] : null), "html", null, true);
        echo "</p>
  ";
        // line 21
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["trace"]) ? $context["trace"] : null));
        foreach ($context['_seq'] as $context["_key"] => $context["linea"]) {
            // line 22
            echo "    <p>";
            echo twig_escape_filter($this->env, (isset($context["linea"]) ? $context["linea"] : null), "html", null, true);
            echo "</p>
  ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['linea'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
    }

    public function getTemplateName()
    {
        return "error.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  93 => 22,  89 => 21,  85 => 20,  81 => 19,  77 => 18,  73 => 17,  70 => 16,  66 => 13,  61 => 12,  55 => 9,  49 => 7,  43 => 4,  38 => 3,  32 => 2,);
    }
}
