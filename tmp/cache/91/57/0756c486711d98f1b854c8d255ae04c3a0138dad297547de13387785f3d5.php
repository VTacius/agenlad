<?php

/* menu.html.twig */
class __TwigTemplate_91570756c486711d98f1b854c8d255ae04c3a0138dad297547de13387785f3d5 extends Twig_Template
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
        // line 1
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["menu"]) ? $context["menu"] : null));
        foreach ($context['_seq'] as $context["archivo"] => $context["titulo"]) {
            // line 2
            echo "    ";
            if (((isset($context["archivo"]) ? $context["archivo"] : null) == (isset($context["pagina"]) ? $context["pagina"] : null))) {
                // line 3
                echo "        <li class=\"active\"><a href=";
                echo twig_escape_filter($this->env, (isset($context["archivo"]) ? $context["archivo"] : null), "html", null, true);
                echo ">";
                echo twig_escape_filter($this->env, (isset($context["titulo"]) ? $context["titulo"] : null), "html", null, true);
                echo "</a></li>
    ";
            } else {
                // line 5
                echo "        <li><a href=";
                echo twig_escape_filter($this->env, (isset($context["archivo"]) ? $context["archivo"] : null), "html", null, true);
                echo ">";
                echo twig_escape_filter($this->env, (isset($context["titulo"]) ? $context["titulo"] : null), "html", null, true);
                echo "</a></li>
    ";
            }
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['archivo'], $context['titulo'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 7
        echo "  
    
";
    }

    public function getTemplateName()
    {
        return "menu.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  46 => 7,  34 => 5,  26 => 3,  23 => 2,  19 => 1,);
    }
}
