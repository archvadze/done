# 📱 Real Device Testing Instructions

## 🚀 Quick Start for Device Testing

### 1. Install Dependencies
```bash
cd /var/www/done/mobile/AcumenCraftMobile
npm install expo-image-picker expo-document-picker
npm install
```

### 2. Start Development Server
```bash
npx expo start
```

### 3. Connect Your Device

#### For Android:
1. Install **Expo Go** from Google Play Store
2. Enable Developer Options and USB Debugging
3. Connect to same WiFi network
4. Scan QR code from terminal/browser

#### For iOS:
1. Install **Expo Go** from App Store
2. Connect to same WiFi network
3. Scan QR code with Camera app or Expo Go

## 📷 Camera Testing Features

### ✅ Test Scenarios

#### 1. **Camera Capture Test**
- Open Upload screen
- Tap "Select Media" → "📷 Camera"
- Grant camera permissions
- Take a photo
- Verify image preview and metadata

#### 2. **Gallery Selection Test**
- Open Upload screen
- Tap "Select Media" → "📱 Photo Library"
- Grant photo library permissions
- Select an image from gallery
- Verify image preview and metadata

#### 3. **File Browser Test**
- Open Upload screen
- Tap "Select Media" → "📁 File Browser"
- Browse and select a file
- Verify file preview and metadata

#### 4. **Upload Process Test**
- Select media via any method
- Fill in artwork details (title, description, tags)
- Select category and license
- Submit upload
- Verify success feedback

### 🔧 Device-Specific Features

#### Permission Handling
- **iOS**: Info.plist descriptions for camera/photo access
- **Android**: Runtime permissions for camera/storage

#### Platform Detection
- App shows platform badge (iOS/Android)
- Platform-specific UI adjustments
- Native camera integration

#### File Management
- Real file size calculation and display
- Image dimensions detection
- File type validation
- Upload progress tracking

## 🎯 Testing Checklist

### Basic Functionality
- [ ] App launches successfully on device
- [ ] Navigation works smoothly
- [ ] Authentication system functions
- [ ] All screens load properly

### Camera Features
- [ ] Camera permission request works
- [ ] Real camera capture functions
- [ ] Photo library access works
- [ ] File browser opens correctly
- [ ] Image preview displays properly
- [ ] File metadata shows correctly

### Upload Process
- [ ] Form validation works
- [ ] Multi-language input functions
- [ ] Category selection works
- [ ] Upload progress shows
- [ ] Success/error messages display

### Performance
- [ ] App is responsive to touch
- [ ] No crashes during testing
- [ ] Smooth animations and transitions
- [ ] Acceptable memory usage

## 🔍 Debugging Tips

### Common Issues

#### Camera Not Working
```bash
# Check permissions in device settings
# Restart Expo development server
npx expo start --clear
```

#### App Crashes
```bash
# Check device logs
npx expo logs

# Clear cache and restart
npx expo start --clear
```

#### Slow Performance
```bash
# Use development build for better performance
npx expo install --fix
npm start
```

### Debug Information
- Check Metro bundler logs in terminal
- Use React Native Debugger if needed
- Monitor device console for errors
- Test on multiple devices/screen sizes

## 📊 Expected Results

### Success Criteria
✅ Camera opens instantly on device tap
✅ Photo capture works without issues  
✅ Gallery selection functions smoothly
✅ File upload completes successfully
✅ App remains stable during testing
✅ UI is responsive and intuitive

### Performance Targets
- **Camera launch**: < 1 second
- **Image selection**: < 2 seconds  
- **Upload initiation**: < 3 seconds
- **Navigation**: < 100ms response
- **Memory usage**: < 150MB

## 🎮 Interactive Testing Commands

```bash
# Start with device-specific mode
npx expo start --android  # For Android testing
npx expo start --ios      # For iOS testing

# Enable debugging
npx expo start --dev-client

# Clear cache if issues occur
npx expo start --clear

# Check logs in real-time
npx expo logs
```

## 📱 Next Testing Phase

After successful device testing:
1. **Performance optimization**
2. **Real backend integration testing**
3. **Multi-device compatibility**
4. **User acceptance testing**
5. **Production build preparation**

---

## 🎉 Ready for Real Device Testing!

The Acumen Craft Mobile app is now equipped with:
- ✅ Real camera integration
- ✅ Device permission handling  
- ✅ Professional upload workflow
- ✅ Cross-platform compatibility
- ✅ Production-ready features

**Start testing now with the commands above!** 📱🚀
