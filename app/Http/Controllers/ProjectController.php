<?php

namespace App\Http\Controllers;

use App\DTO\ProjectDetails;
use App\Models\Project;
use App\Services\ProjectProviderRegistry;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function show(
        Request                 $request,
        string                  $provider,
        string                  $id,
        ProjectProviderRegistry $providers,
    )
    {
        abort_unless($providers->has($provider), 404);

        $details = $providers
            ->get($provider)
            ->project($id);

        abort_if($details === null, 404);

        $additionalDetails = collect();

        foreach ($request->array('sources') as $sourceProvider => $sourceId) {
            if (
                $sourceProvider === $provider
                || !$providers->has($sourceProvider)
                || !is_string($sourceId)
                || $sourceId === ''
            ) {
                continue;
            }

            $sourceDetails = $providers
                ->get($sourceProvider)
                ->project($sourceId);

            if ($sourceDetails !== null) {
                $additionalDetails->push($sourceDetails);
            }
        }

        $details = $this->mergeDetails(
            $details,
            $additionalDetails->all(),
        );

        return view('project', [
            'details' => $details,
            'project' => $details->project,
            'versions' => $details->versions,
        ]);
    }

    /**
     * @param array<int, ProjectDetails> $others
     */
    private function mergeDetails(
        ProjectDetails $primary,
        array          $others,
    ): ProjectDetails
    {
        $project = $primary->project;

        foreach ($others as $details) {
            $other = $details->project;

            $project = new Project(
                id: $project->id(),
                name: $project->name(),
                slug: $project->slug(),
                summary: $project->summary() ?? $other->summary(),
                iconUrl: $project->iconUrl() ?? $other->iconUrl(),
                author: $project->author() ?? $other->author(),
                downloads: max(
                    $project->downloads(),
                    $other->downloads(),
                ),
                categories: array_values(array_unique([
                    ...$project->categories(),
                    ...$other->categories(),
                ])),
                versions: array_values(array_unique([
                    ...$project->versions(),
                    ...$other->versions(),
                ])),
                loaders: array_values(array_unique([
                    ...$project->loaders(),
                    ...$other->loaders(),
                ])),
                projectTypes: array_values(array_unique([
                    ...$project->projectTypes(),
                    ...$other->projectTypes(),
                ])),
                sources: collect([
                    ...$project->sources(),
                    ...$other->sources(),
                ])
                    ->unique(
                        fn(
                            array $source
                        ) => ($source['platform'] ?? '')
                            . ':'
                            . ($source['id'] ?? ''),
                    )
                    ->values()
                    ->all(),
                likes: max(
                    $project->likes(),
                    $other->likes()
                ),
            );
        }

        return new ProjectDetails(
            project: $project,
            description: $primary->description
            ?? collect($others)->pluck('description')->filter()->first(),
            likes: collect([
                $primary->likes,
                ...collect($others)->pluck('likes')->all()
            ])
            ->map(fn(
                $likes
            ) => $likes)
            ->max() ?? 0,
            publishedAt: $primary->publishedAt,
            updatedAt: $primary->updatedAt,
            license: $primary->license,
            environment: $primary->environment,
            gallery: $primary->gallery,
            versions: collect([
                $primary,
                ...$others,
            ])
                ->flatMap(fn(
                    ProjectDetails $details
                ) => $details->versions)
                ->values()
                ->all(),
            links: collect([
                $primary,
                ...$others,
            ])
                ->flatMap(fn(
                    ProjectDetails $details
                ) => $details->links)
                ->values()
                ->all(),
            downloadUrl: $primary->downloadUrl
            ?? collect($others)->pluck('downloadUrl')->filter()->first(),
            downloads: collect([$primary, ...$others])
                ->flatMap(fn(
                    ProjectDetails $details
                ) => $details->downloads)
                ->filter(fn(
                    $download
                ) => is_array($download) && !empty($download['url']))
                ->unique(fn(
                    array $download
                ) => ($download['provider'] ?? '') . ':' . ($download['url'] ?? ''))
                ->values()
                ->all(),
            metadata: $primary->metadata,
        );
    }
}
