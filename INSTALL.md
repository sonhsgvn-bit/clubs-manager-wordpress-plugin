# Installation and Testing Guide

## Quick Setup

1. **Upload Plugin**
   ```bash
   # Upload the entire 'clubs-manager' folder to:
   /wp-content/plugins/clubs-manager/
   ```

2. **Activate Plugin**
   - Go to WordPress Admin → Plugins
   - Find "Clubs Manager" and click "Activate"
   - Plugin will automatically flush rewrite rules

3. **Verify URLs Work**
   - Visit: `your-site.com/club/` (should show archive page)
   - No 404 errors should occur

## Test the Plugin

### 1. Add a Test Club
- Go to WordPress Admin → Câu lạc bộ → Thêm mới
- Add title: "Test Billiard Club"
- Add content and fill meta fields
- Set featured image
- Publish

### 2. Test URLs
- Archive: `your-site.com/club/`
- Single: `your-site.com/club/test-billiard-club/`
- Both should load without 404 errors

### 3. Add Taxonomy Terms
- Go to Câu lạc bộ → Khu vực
- Add areas like "Quận 1", "Quận 3", etc.

### 4. Configure Google Maps (Optional)
- Get API key from Google Cloud Console
- Go to Câu lạc bộ → Cài đặt
- Enter API key

### 5. Test Shortcodes
Add to any page/post:
```
[clubs_list]
[clubs_search]
[clubs_map]
```

## Verify Fixed Issues

### ✅ Custom Post Type URLs
- `/club/` - Archive page works
- `/club/club-name/` - Single page works
- No more "Page Not Found" errors

### ✅ Permalink Structure
- Pretty URLs are generated correctly
- Rewrite rules are flushed on activation

### ✅ Template Loading
- Plugin templates load instead of theme's 404 page
- Custom styling is applied

## Troubleshooting

### URLs Still Show 404?
1. Go to Settings → Permalinks
2. Click "Save Changes" to flush rewrite rules
3. Or deactivate/reactivate the plugin

### Templates Not Loading?
- Check if template files exist in plugin
- Verify template_include filter is working
- Check for theme conflicts

### No Styling?
- Check if CSS files are enqueued
- Look for JavaScript console errors
- Verify plugin URLs are correct

## File Permissions
Ensure these files are readable:
```
clubs-manager/
├── clubs-manager.php (644)
├── includes/ (755)
├── templates/ (755)
├── assets/ (755)
└── all files (644)
```