<?php


namespace App\GraphQL\Queries;

use App\Facades\App;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class AttributeQuery
{
  public function index($rootValue, array $args, GraphQLContext $context)
  {
    return App::call('AttributeController@index', $args);
  }

  public function getAttributesExportUrl($rootValue, array $args, GraphQLContext $context)
  {
    return App::call('AttributeController@getAttributesExportUrl', $args);
  }
}
