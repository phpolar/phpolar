<?php

declare(strict_types=1);

namespace Phpolar\Phpolar;

use Phpolar\Phpolar\Model\AbstractModel;
use Phpolar\Phpolar\Model\Model;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionParameter;
use RuntimeException;

/**
 * Converts an object that is marked as a model
 * attribute to a argument-name-object key-value pair.
 */
final class ModelResolver
{
    /**
     * @param ReflectionMethod $reflectionMethod
     * @param array<string,string>|object|null $parsedRequestBody
     */
    public function __construct(
        private ReflectionMethod $reflectionMethod,
        private array|object|null $parsedRequestBody,
    ) {
    }

    /**
     * Return the argument-name, object key-value pair
     * of the Model.
     *
     * @return array<string,AbstractModel>
     */
    public function resolve(): array
    {
        $methodArgs = $this->reflectionMethod->getParameters();
        $modelParams = array_filter($methodArgs, $this->hasModelParams(...));
        $modelParamNames = array_map($this->getParamName(...), $modelParams);
        $modelInstances = array_map($this->newModel(...), $modelParams);
        return array_filter(
            array_combine(
                $modelParamNames,
                $modelInstances,
            )
        );
    }

    private function getParamName(ReflectionParameter $param): string
    {
        return $param->getName();
    }

    private function hasModelParams(ReflectionParameter $param): bool
    {
        return count($param->getAttributes(Model::class)) > 0;
    }

    private function newModel(ReflectionParameter $param): ?AbstractModel
    {
        $paramType = $param->getType();
        if ($paramType instanceof ReflectionNamedType) {
            $className = $paramType->getName();
            if (is_subclass_of($className, AbstractModel::class) === false) {
                throw new RuntimeException(
                    sprintf(
                        "Argument of type %s is not a subclass of %s",
                        $className,
                        AbstractModel::class
                    )
                );
            }
            return new $className($this->parsedRequestBody);
        }
        return null; // @codeCoverageIgnore
    }
}
