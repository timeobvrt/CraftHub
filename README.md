<p align="center">
    <img
        src="./public/crafthub-text-horizontal.svg"
        alt="CraftHub"
        width="420"
    >
</p>

<h1 align="center">CraftHub</h1>

<p align="center">
    A unified search platform for discovering Minecraft projects across multiple hosting platforms.
</p>

# CraftHub

CraftHub is a unified search platform for discovering Minecraft mods, plugins, modpacks, resource packs, shaders, and
other community projects across multiple hosting platforms.

Instead of searching each platform individually, CraftHub collects projects from supported providers, normalizes their
information, detects duplicates, and presents everything through a consistent interface.

## Features

- Search projects across multiple platforms.
- Current support for Modrinth and SpigotMC.
- Unified project cards and project pages.
- Platform-specific download links.

## Multi-platform projects

A project may exist on multiple platforms with different metadata.

For example, a project can be distributed as:

- a mod on Modrinth;
- a plugin on SpigotMC;
- a Paper plugin on Hangar.

CraftHub can merge these entries into one project while preserving:

- every source platform;
- project types;
- supported loaders;
- Minecraft versions;
- categories;
- download links;
- platform-specific metadata.

This prevents duplicate search results without losing useful information.

## Supported providers

### Modrinth

Modrinth provides:

- mods, plugins, modpacks, shaders, resource packs, server;
- detailed descriptions;
- project galleries;
- version-specific files;
- supported loaders and Minecraft versions;
- followers, environments, and external links.

### SpigotMC

SpigotMC resources are accessed through the Spiget API and provide:

- Bukkit, Spigot, Paper, and Purpur plugins;
- tested Minecraft versions;
- resource descriptions and summaries;
- download counts;
- resource and download links.
