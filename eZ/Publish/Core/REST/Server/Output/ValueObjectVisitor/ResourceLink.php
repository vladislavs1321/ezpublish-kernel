<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace eZ\Publish\Core\REST\Server\Output\ValueObjectVisitor;

use eZ\Publish\API\Repository\Exceptions\UnauthorizedException as ApiUnauthorizedException;
use eZ\Publish\Core\MVC\Symfony\RequestStackAware;
use eZ\Publish\Core\REST\Common\Output\Generator;
use eZ\Publish\Core\REST\Common\Output\ValueObjectVisitor;
use eZ\Publish\Core\REST\Common\Output\Visitor;
use eZ\Publish\Core\REST\Server\ValueLoaders\UriValueLoader;

class ResourceLink extends ValueObjectVisitor
{
    use RequestStackAware;

    /**
     * @var UriValueLoader
     */
    private $valueLoader;

    public function __construct(UriValueLoader $valueLoader)
    {
        $this->valueLoader = $valueLoader;
    }

    /**
     * @param Visitor $visitor
     * @param Generator $generator
     * @param \eZ\Publish\Core\REST\Server\Values\ResourceLink $data
     */
    public function visit(Visitor $visitor, Generator $generator, $data)
    {
        $request = $this->getRequestStack()->getMasterRequest();
        $expandedPathList = [];
        if ($request->headers->has('x-ez-embed-value')) {
            $expandedPathList = explode(',', $request->headers->get('x-ez-embed-value'));
        }

        $generator->startAttribute('href', $data->link);
        $generator->endAttribute('href');

        if (in_array($generator->getStackPath(), $expandedPathList)) {
            try {
                $visitor->visitValueObject($this->valueLoader->load($data->link));
            } catch (ApiUnauthorizedException $e) {
            }
        }
    }
}
