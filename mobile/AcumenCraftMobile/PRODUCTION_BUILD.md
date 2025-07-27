# 🚀 Production Build Guide - Acumen Craft Mobile

## 📋 Pre-Build Checklist

### ✅ Environment Setup
- [ ] EAS CLI installed: `npm install -g @expo/cli eas-cli`
- [ ] Expo account configured: `eas login`
- [ ] Project initialized: `eas build:configure`
- [ ] Production environment variables set
- [ ] API endpoints updated for production

### ✅ App Configuration
- [ ] Bundle identifiers configured (iOS/Android)
- [ ] App icons and splash screens optimized
- [ ] Permissions properly configured
- [ ] Deep linking schemes set up
- [ ] Version numbers updated

### ✅ Code Quality
- [ ] All TypeScript errors resolved
- [ ] ESLint warnings addressed
- [ ] Production API endpoints configured
- [ ] Debug code removed
- [ ] Analytics and crash reporting integrated

## 🔧 Build Configuration

### 1. Initialize EAS Build
```bash
# Navigate to project directory
cd /var/www/done/mobile/AcumenCraftMobile

# Install EAS CLI globally
npm install -g @expo/cli eas-cli

# Login to Expo account
eas login

# Initialize EAS build configuration
eas build:configure
```

### 2. Environment Configuration
```bash
# Set production environment variables
export NODE_ENV=production
export API_BASE_URL=https://api.acumencraft.com/v1

# Update version for production
expo install --fix
```

### 3. Build Commands

#### Preview Build (Internal Testing)
```bash
# Build preview version for testing
eas build --profile preview --platform all

# Build for specific platform
eas build --profile preview --platform android
eas build --profile preview --platform ios
```

#### Production Build
```bash
# Build production version
eas build --profile production --platform all

# Build for Android Play Store
eas build --profile production --platform android

# Build for iOS App Store
eas build --profile production --platform ios
```

#### Development Build (for testing)
```bash
# Build development client
eas build --profile development --platform all
```

## 📱 Platform-Specific Configuration

### iOS Production Setup

#### App Store Connect Requirements
1. **Bundle Identifier**: `com.acumencraft.mobile`
2. **Version**: `1.0.0`
3. **Build Number**: Auto-increment enabled
4. **Deployment Target**: iOS 13.0+
5. **Permissions**: Camera, Photo Library access

#### Build Configuration
```json
{
  "ios": {
    "bundleIdentifier": "com.acumencraft.mobile",
    "buildNumber": "1",
    "supportsTablet": true,
    "infoPlist": {
      "NSCameraUsageDescription": "This app needs access to camera to capture and upload artwork photos.",
      "NSPhotoLibraryUsageDescription": "This app needs access to photo library to select and upload artwork images."
    }
  }
}
```

### Android Production Setup

#### Google Play Console Requirements
1. **Package Name**: `com.acumencraft.mobile`
2. **Version Code**: `1`
3. **Target SDK**: 34 (Android 14)
4. **Min SDK**: 21 (Android 5.0)
5. **Permissions**: Camera, Storage access

#### Build Configuration
```json
{
  "android": {
    "package": "com.acumencraft.mobile",
    "versionCode": 1,
    "permissions": [
      "android.permission.CAMERA",
      "android.permission.READ_EXTERNAL_STORAGE",
      "android.permission.WRITE_EXTERNAL_STORAGE"
    ]
  }
}
```

## 🔐 Security & Compliance

### Code Signing
- **iOS**: Apple Developer Account certificates
- **Android**: Upload key and signing key management
- **Secure**: Private keys never committed to repository

### Privacy & Permissions
- **Camera**: Explicit permission requests with clear descriptions
- **Storage**: Scoped storage access on Android 11+
- **Network**: HTTPS-only communication in production
- **Data**: Encryption for sensitive user data

### App Store Guidelines
- **Content**: Family-friendly artwork platform
- **Privacy**: Clear privacy policy and data handling
- **Monetization**: In-app purchases for premium features
- **Marketing**: App Store Optimization (ASO) ready

## 📊 Build Monitoring

### Build Status Tracking
```bash
# Check build status
eas build:list

# View specific build details
eas build:view [BUILD_ID]

# Cancel running build
eas build:cancel [BUILD_ID]
```

### Automated Builds
```bash
# Set up automated builds on code push
eas build --profile production --auto-submit

# Schedule regular builds
eas build --profile production --platform all --non-interactive
```

## 🚢 Deployment Process

### 1. Pre-Deployment Testing
```bash
# Run production build locally for testing
eas build --profile preview --local

# Test on physical devices
expo install --dev-client
eas build --profile development
```

### 2. Store Submission

#### iOS App Store
```bash
# Submit to App Store
eas submit --platform ios

# Monitor submission status
eas submit:list
```

#### Google Play Store
```bash
# Submit to Google Play
eas submit --platform android

# Monitor submission status
eas submit:list
```

### 3. OTA Updates (Post-Release)
```bash
# Push over-the-air update
eas update --branch production --message "Bug fixes and improvements"

# Target specific runtime version
eas update --branch production --runtime-version 1.0.0
```

## 📈 Post-Build Optimization

### Performance Monitoring
- **Bundle Size Analysis**: Track app size growth
- **Runtime Performance**: Monitor app launch time
- **Crash Reporting**: Integrate Sentry or Bugsnag
- **Analytics**: User behavior and feature usage

### Continuous Integration
```bash
# GitHub Actions for automated builds
name: EAS Build
on:
  push:
    branches: [main]
jobs:
  build:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v3
      - uses: actions/setup-node@v3
      - run: npm ci
      - run: eas build --platform all --non-interactive
```

## 🎯 Production Readiness Checklist

### ✅ Technical Requirements
- [ ] TypeScript compilation passes
- [ ] All tests passing
- [ ] Production API endpoints working
- [ ] Error handling implemented
- [ ] Loading states implemented
- [ ] Offline functionality working

### ✅ User Experience
- [ ] Onboarding flow complete
- [ ] Navigation intuitive
- [ ] Accessibility features implemented
- [ ] Multiple screen sizes supported
- [ ] Dark mode support (optional)

### ✅ Business Requirements
- [ ] Terms of Service integrated
- [ ] Privacy Policy accessible
- [ ] User authentication secure
- [ ] Content moderation in place
- [ ] Analytics tracking implemented

### ✅ Store Requirements
- [ ] App icons in all required sizes
- [ ] Screenshots for store listing
- [ ] App description optimized
- [ ] Keywords for ASO selected
- [ ] Age rating appropriate

## 🚀 Build Commands Summary

```bash
# Complete production build workflow
npm install                              # Install dependencies
eas login                               # Login to Expo
eas build:configure                     # Configure build
eas build --profile production --platform all  # Build for production
eas submit --platform all              # Submit to stores

# Monitor and update
eas build:list                          # Check build status
eas submit:list                         # Check submission status
eas update --branch production          # Push OTA updates
```

## 📱 Success Criteria

The production build is ready when:

✅ **Builds Complete Successfully**
- iOS and Android builds finish without errors
- App size within store limits (<100MB initial download)
- Performance benchmarks met

✅ **Store Compliance**
- All store guidelines followed
- Required metadata provided
- Privacy policy accessible

✅ **User Testing**
- Beta testing completed successfully
- Critical bugs resolved
- User feedback incorporated

✅ **Business Goals**
- Core features functional
- Revenue model implemented
- Support system in place

---

## 🎉 Ready for Production Release!

The Acumen Craft Mobile app is now ready for production build and store submission. Follow the step-by-step guide above to deploy to iOS App Store and Google Play Store successfully.
