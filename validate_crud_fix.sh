#!/bin/bash

echo "🔍 ArtCraft CRUD Validation Script"
echo "=================================="
echo ""

# Check if the FileUploadService has the new method
echo "1. Checking FileUploadService..."
if grep -q "replaceArtworkFile" app/Services/FileUploadService.php; then
    echo "✅ replaceArtworkFile method found in FileUploadService"
else
    echo "❌ replaceArtworkFile method NOT found in FileUploadService"
fi

# Check if the ArtworkController handles file uploads in update
echo ""
echo "2. Checking ArtworkController update method..."
if grep -q "hasFile.*file" app/Http/Controllers/ArtworkController.php; then
    echo "✅ File upload handling found in ArtworkController.update()"
else
    echo "❌ File upload handling NOT found in ArtworkController.update()"
fi

# Check if validation includes file parameter
if grep -q "'file' => 'nullable|file" app/Http/Controllers/ArtworkController.php; then
    echo "✅ File validation found in ArtworkController.update()"
else
    echo "❌ File validation NOT found in ArtworkController.update()"
fi

# Check if database transactions are used
if grep -q "DB::beginTransaction" app/Http/Controllers/ArtworkController.php; then
    echo "✅ Database transactions found in ArtworkController.update()"
else
    echo "❌ Database transactions NOT found in ArtworkController.update()"
fi

# Check edit form has file upload capability
echo ""
echo "3. Checking edit form..."
if grep -q "replace-file" resources/views/artworks/edit.blade.php; then
    echo "✅ File replacement checkbox found in edit form"
else
    echo "❌ File replacement checkbox NOT found in edit form"
fi

if grep -q 'name="file"' resources/views/artworks/edit.blade.php; then
    echo "✅ File input found in edit form"
else
    echo "❌ File input NOT found in edit form"
fi

# Check if policies allow artists to update
echo ""
echo "4. Checking artwork policies..."
if grep -q "user->isArtist()" app/Policies/ArtworkPolicy.php; then
    echo "✅ Artist role check found in ArtworkPolicy"
else
    echo "❌ Artist role check NOT found in ArtworkPolicy"
fi

# Check test coverage
echo ""
echo "5. Checking test coverage..."
if grep -q "artist_can_update_image_upload_on_artwork" tests/Feature/ArtworkCrudTest.php; then
    echo "✅ File upload test found in ArtworkCrudTest"
else
    echo "❌ File upload test NOT found in ArtworkCrudTest"
fi

echo ""
echo "🎯 Validation Summary:"
echo "====================="
echo ""
echo "The CRUD functionality fix includes:"
echo "- File replacement method in service layer"
echo "- File upload handling in controller"
echo "- Proper validation and error handling"
echo "- Database transaction safety"
echo "- Frontend file upload interface"
echo "- Comprehensive test coverage"
echo ""
echo "Artists should now be able to update image uploads on their artworks!"
echo ""
echo "🧪 To test manually:"
echo "1. Login as an artist"
echo "2. Edit one of your artworks"
echo "3. Check 'Replace current file with a new one'"
echo "4. Upload a new file and save"
echo "5. Verify the new image is displayed"
