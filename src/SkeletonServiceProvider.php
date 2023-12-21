<?php

declare(strict_types=1);

namespace VendorName\Skeleton;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use VendorName\Skeleton\Commands\SkeletonCommand;

class SkeletonServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {/** <delete> */ /** @formatter:off */ /** </delete> */
        $package->name('skeleton')/** <hasConfig> */
                ->hasConfigFile()/** </hasConfig> *//** <hasViews> */
                ->hasViews()/** </hasViews> *//** <hasDatabase> */
                ->hasMigration('create_skeleton_table')/** </hasDatabase> *//** <hasCommand> */
                ->hasCommand(SkeletonCommand::class)/** </hasCommand> */;
    }
}
