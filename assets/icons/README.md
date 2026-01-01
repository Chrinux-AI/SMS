# Favicon & Icon Files

This directory should contain all favicon and icon files for the Verdant SMS project.

## Required Files

- `favicon.ico` - Multi-resolution ICO file
- `favicon-16x16.png` - 16x16 PNG
- `favicon-32x32.png` - 32x32 PNG
- `favicon-96x96.png` - 96x96 PNG
- `favicon-192x192.png` - 192x192 PNG
- `favicon-512x512.png` - 512x512 PNG
- `apple-touch-icon.png` - 180x180 PNG (for iOS)
- `android-chrome-192x192.png` - 192x192 PNG (for Android)
- `android-chrome-512x512.png` - 512x512 PNG (for Android)
- `mstile-150x150.png` - 150x150 PNG (for Windows)

## How to Generate

1. **Using Online Tool (Recommended):**

   - Visit: https://realfavicongenerator.net/
   - Upload a 512x512px PNG or SVG file
   - Download the generated package
   - Extract all files to this directory

2. **Using Command Line:**

   ```bash
   # If you have ImageMagick installed
   convert source-icon.png -resize 16x16 favicon-16x16.png
   convert source-icon.png -resize 32x32 favicon-32x32.png
   # ... and so on
   ```

3. **Design Guidelines:**
   - Use Verdant green (#22C55E) and cyber blue (#00D9FF)
   - Include "V" letter or leaf symbol
   - Ensure icons are clear at small sizes
   - Test on different backgrounds (light/dark)

## Current Status

⚠️ **Icons need to be generated** - Use the source SVG at `/assets/images/favicon.svg` or create new icons.

## Source File

The source SVG favicon is located at: `/assets/images/favicon.svg`

You can use this as a base to generate all required icon sizes.
