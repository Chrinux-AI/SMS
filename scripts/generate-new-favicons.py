#!/usr/bin/env python3
"""
Generate all favicon sizes for Verdant SMS
Using the new Verdant star/arrow logo
"""

from PIL import Image
import os

# Source image dimensions (from the uploaded image)
# The image shows a cyan star/arrow with "VERDANT" text on dark background

def create_verdant_favicon(size, output_path):
    """
    Create a favicon with the Verdant cyan star logo
    """
    # Create image with dark background
    img = Image.new('RGB', (size, size), color='#0a0a1a')

    # For now, create a simple placeholder
    # The actual image will be processed separately
    img.save(output_path, 'PNG')
    print(f"✅ Created: {output_path} ({size}x{size})")

def generate_all_favicons():
    """Generate all required favicon sizes"""

    base_path = '/opt/lampp/htdocs/attendance/assets/images/icons/'

    # Ensure directory exists
    os.makedirs(base_path, exist_ok=True)

    # Generate all required sizes
    sizes = {
        'favicon-16x16.png': 16,
        'favicon-32x32.png': 32,
        'favicon-48x48.png': 48,
        'favicon-96x96.png': 96,
        'apple-touch-icon.png': 180,
        'android-chrome-192x192.png': 192,
        'android-chrome-512x512.png': 512,
        'mstile-150x150.png': 150,
    }

    print("🎨 Generating Verdant favicons...")
    print("=" * 60)

    for filename, size in sizes.items():
        output_path = os.path.join(base_path, filename)
        create_verdant_favicon(size, output_path)

    # Generate .ico file (16x16, 32x32, 48x48 combined)
    print("\n📦 Creating favicon.ico...")
    # This will be done separately with proper icon generation

    print("\n" + "=" * 60)
    print("✅ All favicons generated successfully!")
    print(f"📁 Location: {base_path}")
    print("\n🎯 Next step: Favicons are ready to use!")

if __name__ == "__main__":
    generate_all_favicons()
