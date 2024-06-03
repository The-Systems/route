<?php
/**
 * Just Framework - It's a PHP micro-framework for Full Stack Web Developer
 *
 * @package     Just Framework
 * @copyright   2016 (c) Mahmoud Elnezamy
 * @author      Mahmoud Elnezamy <http://nezamy.com>
 * @link        http://justframework.com
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 * @version     1.0.0
 */

namespace TheSystems\Router;
/**
 * App
 *
 * @package     Just Framework
 * @author      Mahmoud Elnezamy <http://nezamy.com>
 * @since       1.0.0
 */
class App
{
  private static $instance;
  private Request $request;
  private Route $route;
  private string $baseDIR;

  /**
   * Constructor - Define some variables.
   */
  public function __construct(string $baseDIR)
  {
    $this->autoload();

    $this->baseDIR = $baseDIR;
    $this->request = Request::instance();
    $this->route = Route::instance($this->request);
  }

  /**
   * Singleton instance.
   *
   * @return $this
   */
  public static function instance(string $baseDIR = __DIR__): static
  {
    if (null === static::$instance) {
      static::$instance = new static($baseDIR);
    }
    return static::$instance;
  }

  /**
   * Magic autoload.
   */
  public function autoload(): void
  {
    spl_autoload_register(function ($className) {
      $className = str_replace("\\", DS, $className);
      $classNameOnly = basename($className);
      $namespace = substr($className, 0, -strlen($classNameOnly));
      if (is_file($class = $this->baseDIR . "{$className}.php")) {
        return include_once($class);
      } elseif (is_file($class = $this->baseDIR . strtolower($namespace) . $classNameOnly . '.php')) {
        return include_once($class);
      } elseif (is_file($class = $this->baseDIR . strtolower($className) . '.php')) {
        return include_once($class);
      } elseif (is_file($class = $this->baseDIR . $namespace . lcfirst($classNameOnly) . '.php')) {
        return include_once($class);
      } elseif (is_file($class = $this->baseDIR . strtolower($namespace) . lcfirst($classNameOnly) . '.php')) {
        return include_once($class);
      }
      return false;
    });
  }

  /**
   * Magic call.
   *
   * @param string $method
   * @param array $args
   *
   * @return mixed
   */
  public function __call($method, $args)
  {
    return isset($this->{$method}) && is_callable($this->{$method})
      ? call_user_func_array($this->{$method}, $args) : null;
  }

  /**
   * Set new variables and functions to this class.
   *
   * @param string $k
   * @param mixed $v
   */
  public function __set($k, $v)
  {
    $this->{$k} = $v instanceof \Closure ? $v->bindTo($this) : $v;
  }

  public function getRequest(): Request
  {
    return $this->request;
  }

  public function getRoute(): Route
  {
    return $this->route;
  }


}
