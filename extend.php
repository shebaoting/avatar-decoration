<?php

namespace Shebaoting\AvatarDecoration;

use Flarum\Api\Context;
use Flarum\Api\Resource;
use Flarum\Api\Schema;
use Flarum\Extend;
use Flarum\Frontend\Document;
use Flarum\User\User;
use Shebaoting\AvatarDecoration\Controller\AssetManifestController;
use Shebaoting\AvatarDecoration\Controller\AvatarAssetController;
use Shebaoting\AvatarDecoration\Support\AvatarAssetRepository;

return [
    (new Extend\Frontend('forum'))
        ->js(__DIR__.'/js/dist/forum.js')
        ->css(__DIR__.'/less/forum.less')
        ->route('/avatar/edit', 'avatar-decoration.edit', function (Document $document) {
            $document->title = 'Style Avatar';
        }),

    (new Extend\Frontend('admin'))
        ->js(__DIR__.'/js/dist/admin.js')
        ->css(__DIR__.'/less/admin.less'),

    new Extend\Locales(__DIR__.'/locale'),

    (new Extend\Model(User::class))
        ->cast('avatar_decoration_updated_at', 'datetime'),

    (new Extend\ApiResource(Resource\UserResource::class))
        ->fields(fn () => [
            Schema\Arr::make('avatarDecoration')
                ->get(fn (User $user) => AvatarAssetRepository::decodeUserDecoration($user->avatar_decoration ?? null))
                ->nullable()
                ->writable(fn (User $user, Context $context) => $context->updating() && AvatarAssetRepository::canEditUser($user, $context))
                ->set(function (User $user, mixed $value) {
                    $normalized = AvatarAssetRepository::normalizeDecoration(is_array($value) ? $value : []);

                    $user->avatar_decoration = json_encode($normalized, JSON_UNESCAPED_SLASHES);
                    $user->avatar_decoration_updated_at = new \DateTimeImmutable();
                }),
            Schema\Boolean::make('canEditAvatarDecoration')
                ->get(fn (User $user, Context $context) => AvatarAssetRepository::canEditUser($user, $context)),
            Schema\DateTime::make('avatarDecorationUpdatedAt')
                ->property('avatar_decoration_updated_at')
                ->nullable(),
        ]),

    (new Extend\ApiResource(Resource\ForumResource::class))
        ->fields(fn () => [
            Schema\Str::make('avatarDecorationManifestUrl')
                ->get(fn () => '/api/avatar-decoration/assets'),
            Schema\Str::make('avatarDecorationAssetVersion')
                ->get(fn () => (string) resolve(AvatarAssetRepository::class)->assetVersion()),
        ]),

    (new Extend\Routes('api'))
        ->get('/avatar-decoration/assets', 'avatar-decoration.assets.index', AssetManifestController::class),

    (new Extend\Routes('forum'))
        ->get('/avatar-decoration/asset', 'avatar-decoration.assets.query', AvatarAssetController::class)
        ->get('/avatar-decoration/assets/{path:.+}', 'avatar-decoration.assets.show', AvatarAssetController::class),
];
