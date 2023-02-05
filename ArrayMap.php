<?php /*﻿*/ declare(strict_types=1);
/**
 * @package ArrayMap
 * @license https://opensource.org/licenses/MIT  MIT License
 * The MIT License (MIT)
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 *
 * Additional options can be provided to control the naming
 * convention of the class files.
 *
 *
 * namespace Example;
 *
 * use function is_null;
 * use function array_keys;
 *
 * @link https://www.php.net/manual/en/book.ds.php
 */
use Ds\Map as DsMap;
use Ds\Set as DsSet;

#[\AllowDynamicProperties]
abstract class ArrayMap implements \IteratorAggregate, \Stringable
{
    private DsMap $map;
    private DsSet $keys;


    /** @access final **/
    final public function __construct(iterable $iterable = [])
    {
        $this->keys = new DsSet(
            ($this->map = new DsMap($iterable))->keys()->toArray()
        );
        foreach ($iterable as $key => $value) {
            $this->__set($key, $value);
        }
    }


    /** @access final **/
    final public function __clone(): void
    {
        $this->map = $this->map->copy();
    }


    /** @access final **/
    final public function __call(string $method, array $args): mixed
    {
        return $this->preg_match_callback($method, $matches)
            ? $this->{$matches[0]}($matches[1], $args[0] ?? NULL)
            : NULL;
    }


    /** @access final **/
    final public function __set(string|int $key = NULL, mixed $value = NULL): void
    {
        $this->set($key, $value);
    }


    /** @access final **/
    final public function __unset(string|int|NULL $key = NULL): void
    {
        $this->unset($key);
    }


    /** @access final **/
    final public function __isset(string|int $key): bool
    {
        return $this->isset($key);
    }


    /** @access final **/
    final public function __get(string|int $key): mixed
    {
        return $this->get($key);
    }


    /** @access final **/
    final public function is_assoc(array $array): bool
    {
        return \array_keys($array = \array_keys($array)) !== $array;
    }


    /** {@inheritdoc} **/
    public function __toString(): string
    {
        return $this->render();
    }


    /** {@inheritdoc} **/
    public function getIterator(): \Traversable
    {
        /**
         * @link https://www.php.net/manual/en/class.iteratoraggregate.php
         * @uses IteratorAggregate
         */
        return new \ArrayIterator($this->map->toArray());
    }


    public function get(string|int $key): mixed
    {
        return $this->map->get($key, NULL);
    }


    public function isset(mixed $key): bool
    {
        return $this->map->hasKey($key);
    }


    public function set(string|int|NULL $key = NULL, mixed $value = NULL): static
    {
        if (is_null($key))
            $this->map->putAll([$value]);
        else {
            $this->is_key($key)
                || $this->map->put(
                    $key,
                    is_null($value) ? new static
                        : (is_array($value) ? new static($value)
                        : $value)
                );
        }
        return $this;
    }


    public function unset(string|int|NULL $key = NULL): mixed
    {
        if (is_null($key)) {
            $this->keys->clear();
            $this->map->clear();
        } else {
            if ($this->isset($key)) {
                $this->keys->remove($key);
                return $this->map->remove($key);
            }
            return NULL;
        }
        return $this;
    }


    public function merge(iterable $merge): static
    {
        $merge = $this->array_merge(
            $this->array(),
            ($merge instanceof self) ? $merge->array() : (array) $merge
        );

        foreach ($merge as $key => $value) {
            $this->set($key, $value);
        }
        return $this;
    }


    public function load(string|iterable $file = []): static
    {
        $file = (array) $file;
        foreach ($file as $file) {
            $this->merge((array) $this->include($file));
        }
        return $this;
    }


    public function array(): array
    {
        $array = [];
        foreach ($this->map as $key => $value) {
            $array[$key] = ($value instanceof self)
                ? $value->array()
                : $value;
        }
        return $array;
    }


    public function include(string $file, iterable $iterable = []): mixed
    {
        static $include;
        if ( ! isset($include)) {
            /** Scope isolated include. Prevents access to $this/self from included files. **/
            $include = static function(string $file, iterable $data = []) use ( & $include): mixed {
                if (is_file($file)) {
                    $data = (new static($data))->setFile($file);
                    // Remove the variable include path
                    unset($file);
                    return include $data->unsetFile();
                }
                return NULL;
            };
        }
        return $include($file, $iterable);
    }


    public function render(string $file): string
    {
        ob_start();
        $this->lcfirst_keys();
        $this->include($file, $this->map->toArray());
        return ob_get_clean();
    }


    /** @access protected **/
    protected function lcfirst_keys(): void
    {
        foreach ($this->map as $key => $value) {
            $this->map->remove($key);
            $this->map->put(\lcfirst($key), $value);
        }
    }


    /** @access protected **/
    protected function is_key(string|int $key): bool
    {
        if ($this->isset($key)) {
            return TRUE;
        }
        $this->keys->add($key);
        return FALSE;
    }


    /** @access protected **/
    protected function array_merge(array $array, array $array2): array
    {
        if ($this->is_assoc($array2))
            foreach($array2 as $key => $value)
                $array[$key] = (is_array($value) && isset($array[$key]) && is_array($array[$key]))
                    ? $this->array_merge($array[$key], $value)
                    : $value;
        else {
            foreach($array2 as $value)
                \in_array($value, $array, TRUE) || $array[] = $value;
        }
        return $array;
    }


    /** @access private **/
    private function preg_match_callback(string $method, & $matches): bool
    {
        $matches = \explode("\0",
            \preg_replace_callback('#^(?P<action>get|set|isset|unset)(?P<value>[A-Za-z0-9_]+)?$#D',
                static function(array $m): mixed {
                    return \call_user_func('\method_exists', __CLASS__, $m['action'])
                        ? \implode("\0", [$m['action'], $m['value']])
                        : NULL;
                },
                $method
            )
       );
       return isset($matches[1]);
    }
}

// EOF
