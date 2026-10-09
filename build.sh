#!/usr/bin/env bash
# Package the plugin into dist/tools-adapter-<version>.zip (+ tools-adapter-latest.zip).
# Only runtime files are shipped: demo templates, dist/ and dev files are excluded.
set -euo pipefail

cd "$(dirname "$0")"

VERSION=$(sed -n "s/^define( 'TOOLS_ADAPTER_VERSION', '\([^']*\)' );/\1/p" tools-adapter.php)
if [ -z "$VERSION" ]; then
	echo "Version introuvable dans tools-adapter.php" >&2
	exit 1
fi

STAGE=$(mktemp -d)
trap 'rm -rf "$STAGE"' EXIT

mkdir -p "$STAGE/tools-adapter" dist
cp -r tools-adapter.php includes assets languages README.md LICENSE "$STAGE/tools-adapter/"

OUT="dist/tools-adapter-${VERSION}.zip"
rm -f "$OUT"
(cd "$STAGE" && zip -rq -X "$OLDPWD/$OUT" tools-adapter -x '*.DS_Store' '*.map')
cp "$OUT" dist/tools-adapter-latest.zip

echo "$OUT ($(du -h "$OUT" | cut -f1))"
