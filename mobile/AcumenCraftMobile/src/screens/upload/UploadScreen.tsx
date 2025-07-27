import React, { useState, useEffect } from 'react';
import {
  View,
  Text,
  StyleSheet,
  SafeAreaView,
  ScrollView,
  TouchableOpacity,
  TextInput,
  Image,
  Alert,
  ActivityIndicator,
  Dimensions,
} from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import * as ImagePicker from 'expo-image-picker';
import * as DocumentPicker from 'expo-document-picker';
import { useAuth } from '../../context/AuthContext';
import { ArtworkService } from '../../services/api/artworkService';
import { useNavigation } from '@react-navigation/native';
import { StackNavigationProp } from '@react-navigation/stack';
import { MainTabParamList } from '../../types';

type UploadNavigationProp = StackNavigationProp<MainTabParamList, 'Upload'>;

const { width } = Dimensions.get('window');

interface UploadFormData {
  title: {
    en: string;
    ka: string;
    de: string;
  };
  description: {
    en: string;
    ka: string;
    de: string;
  };
  tags: string;
  license: string;
  visibility: string;
  category: string;
  isAiGenerated: boolean;
}

const UploadScreen: React.FC = () => {
  const navigation = useNavigation<UploadNavigationProp>();
  const { state } = useAuth();
  const [selectedMedia, setSelectedMedia] = useState<any>(null);
  const [uploading, setUploading] = useState(false);
  const [activeLanguage, setActiveLanguage] = useState<'en' | 'ka' | 'de'>('en');

  const [formData, setFormData] = useState<UploadFormData>({
    title: {
      en: '',
      ka: '',
      de: '',
    },
    description: {
      en: '',
      ka: '',
      de: '',
    },
    tags: '',
    license: 'CC BY-SA',
    visibility: 'public',
    category: 'other',
    isAiGenerated: false,
  });

  const artworkService = new ArtworkService();

  // Request permissions
  useEffect(() => {
    requestPermissions();
  }, []);

  const requestPermissions = async () => {
    const { status: cameraStatus } = await ImagePicker.requestCameraPermissionsAsync();
    const { status: libraryStatus } = await ImagePicker.requestMediaLibraryPermissionsAsync();

    if (cameraStatus !== 'granted' || libraryStatus !== 'granted') {
      Alert.alert(
        'Permissions needed',
        'Camera and photo library access are required to upload artwork.',
        [{ text: 'OK' }]
      );
    }
  };

  // Show media picker options
  const showMediaPicker = () => {
    Alert.alert(
      'Select Media',
      'Choose how you want to add your artwork',
      [
        { text: 'Camera', onPress: openCamera },
        { text: 'Photo Library', onPress: openImagePicker },
        { text: 'File Browser', onPress: openDocumentPicker },
        { text: 'Cancel', style: 'cancel' },
      ]
    );
  };

  // Open camera
  const openCamera = async () => {
    try {
      const result = await ImagePicker.launchCameraAsync({
        mediaTypes: ImagePicker.MediaTypeOptions.Images,
        allowsEditing: true,
        aspect: [4, 3],
        quality: 0.8,
      });

      if (!result.canceled && result.assets[0]) {
        setSelectedMedia(result.assets[0]);
      }
    } catch (error) {
      console.error('Camera error:', error);
      Alert.alert('Error', 'Failed to open camera');
    }
  };

  // Open image picker
  const openImagePicker = async () => {
    try {
      const result = await ImagePicker.launchImageLibraryAsync({
        mediaTypes: ImagePicker.MediaTypeOptions.Images,
        allowsEditing: true,
        aspect: [4, 3],
        quality: 0.8,
      });

      if (!result.canceled && result.assets[0]) {
        setSelectedMedia(result.assets[0]);
      }
    } catch (error) {
      console.error('Image picker error:', error);
      Alert.alert('Error', 'Failed to open image picker');
    }
  };

  // Open document picker
  const openDocumentPicker = async () => {
    try {
      const result = await DocumentPicker.getDocumentAsync({
        type: ['image/*', 'video/*'],
        copyToCacheDirectory: true,
      });

      if (!result.canceled && result.assets[0]) {
        setSelectedMedia(result.assets[0]);
      }
    } catch (error) {
      console.error('Document picker error:', error);
      Alert.alert('Error', 'Failed to open file browser');
    }
  };

  // Update form field
  const updateFormField = (field: keyof UploadFormData, value: any) => {
    setFormData(prev => ({
      ...prev,
      [field]: value,
    }));
  };

  // Update multilingual field
  const updateMultilingualField = (
    field: 'title' | 'description',
    language: 'en' | 'ka' | 'de',
    value: string
  ) => {
    setFormData(prev => ({
      ...prev,
      [field]: {
        ...prev[field],
        [language]: value,
      },
    }));
  };

  // Validate form
  const validateForm = (): boolean => {
    if (!selectedMedia) {
      Alert.alert('Error', 'Please select a media file');
      return false;
    }

    if (!formData.title.en.trim()) {
      Alert.alert('Error', 'Please enter a title in English');
      return false;
    }

    if (!formData.description.en.trim()) {
      Alert.alert('Error', 'Please enter a description in English');
      return false;
    }

    return true;
  };

  // Submit upload
  const handleSubmit = async () => {
    if (!validateForm()) return;

    setUploading(true);
    try {
      const uploadData = {
        file: selectedMedia,
        title: formData.title,
        description: formData.description,
        tags: formData.tags,
        license: formData.license,
        visibility: formData.visibility,
        category: formData.category,
        is_ai_generated: formData.isAiGenerated,
      };

      await artworkService.uploadArtwork(uploadData);

      Alert.alert(
        'Success',
        'Your artwork has been uploaded successfully!',
        [
          {
            text: 'OK',
            onPress: () => {
              // Reset form
              setSelectedMedia(null);
              setFormData({
                title: { en: '', ka: '', de: '' },
                description: { en: '', ka: '', de: '' },
                tags: '',
                license: 'CC BY-SA',
                visibility: 'public',
                category: 'other',
                isAiGenerated: false,
              });
              // Navigate to explore or home
              navigation.navigate('Explore');
            },
          },
        ]
      );
    } catch (error) {
      console.error('Upload error:', error);
      Alert.alert('Error', 'Failed to upload artwork. Please try again.');
    } finally {
      setUploading(false);
    }
  };

  const categories = [
    { value: 'traditional_art', label: 'Traditional Art' },
    { value: 'digital_art', label: 'Digital Art' },
    { value: 'photography', label: 'Photography' },
    { value: 'sculpture', label: 'Sculpture' },
    { value: 'crafts', label: 'Crafts' },
    { value: 'mixed_media', label: 'Mixed Media' },
    { value: 'other', label: 'Other' },
  ];

  const licenses = [
    { value: 'CC BY', label: 'CC BY - Attribution' },
    { value: 'CC BY-SA', label: 'CC BY-SA - Attribution-ShareAlike' },
    { value: 'CC BY-NC', label: 'CC BY-NC - Attribution-NonCommercial' },
    { value: 'CC BY-ND', label: 'CC BY-ND - Attribution-NoDerivs' },
    { value: 'All Rights Reserved', label: 'All Rights Reserved' },
  ];

  const visibilityOptions = [
    { value: 'public', label: 'Public - Everyone can see' },
    { value: 'private', label: 'Private - Only you can see' },
    { value: 'unlisted', label: 'Unlisted - Only with link' },
  ];

  return (
    <SafeAreaView style={styles.container}>
      <ScrollView style={styles.scrollView}>
        {/* Header */}
        <View style={styles.header}>
          <Text style={styles.headerTitle}>Upload Artwork</Text>
          <Text style={styles.headerSubtitle}>Share your creative work with the community</Text>
        </View>

        {/* Media Selection */}
        <View style={styles.section}>
          <Text style={styles.sectionTitle}>Select Media</Text>
          {selectedMedia ? (
            <View style={styles.mediaContainer}>
              <Image source={{ uri: selectedMedia.uri }} style={styles.selectedImage} />
              <View style={styles.mediaInfo}>
                <Text style={styles.mediaName}>{selectedMedia.name || 'Selected media'}</Text>
                <Text style={styles.mediaSize}>
                  {selectedMedia.size ? `${(selectedMedia.size / 1024 / 1024).toFixed(2)} MB` : 'Unknown size'}
                </Text>
              </View>
              <TouchableOpacity style={styles.changeMediaButton} onPress={showMediaPicker}>
                <Ionicons name="refresh" size={20} color="#007AFF" />
                <Text style={styles.changeMediaText}>Change</Text>
              </TouchableOpacity>
            </View>
          ) : (
            <TouchableOpacity style={styles.mediaSelector} onPress={showMediaPicker}>
              <Ionicons name="cloud-upload-outline" size={48} color="#007AFF" />
              <Text style={styles.mediaSelectorText}>Tap to select media</Text>
              <Text style={styles.mediaSelectorSubtext}>Camera, Gallery, or Files</Text>
            </TouchableOpacity>
          )}
        </View>

        {/* Language Selector */}
        <View style={styles.section}>
          <Text style={styles.sectionTitle}>Language</Text>
          <View style={styles.languageSelector}>
            {(['en', 'ka', 'de'] as const).map((lang) => (
              <TouchableOpacity
                key={lang}
                style={[
                  styles.languageButton,
                  activeLanguage === lang && styles.languageButtonActive,
                ]}
                onPress={() => setActiveLanguage(lang)}
              >
                <Text
                  style={[
                    styles.languageButtonText,
                    activeLanguage === lang && styles.languageButtonTextActive,
                  ]}
                >
                  {lang.toUpperCase()}
                </Text>
              </TouchableOpacity>
            ))}
          </View>
        </View>

        {/* Title Input */}
        <View style={styles.section}>
          <Text style={styles.sectionTitle}>Title ({activeLanguage.toUpperCase()})</Text>
          <TextInput
            style={styles.textInput}
            value={formData.title[activeLanguage]}
            onChangeText={(text) => updateMultilingualField('title', activeLanguage, text)}
            placeholder={`Enter title in ${activeLanguage.toUpperCase()}`}
            maxLength={100}
          />
        </View>

        {/* Description Input */}
        <View style={styles.section}>
          <Text style={styles.sectionTitle}>Description ({activeLanguage.toUpperCase()})</Text>
          <TextInput
            style={[styles.textInput, styles.textArea]}
            value={formData.description[activeLanguage]}
            onChangeText={(text) => updateMultilingualField('description', activeLanguage, text)}
            placeholder={`Enter description in ${activeLanguage.toUpperCase()}`}
            multiline
            numberOfLines={4}
            maxLength={500}
          />
        </View>

        {/* Tags Input */}
        <View style={styles.section}>
          <Text style={styles.sectionTitle}>Tags</Text>
          <TextInput
            style={styles.textInput}
            value={formData.tags}
            onChangeText={(text) => updateFormField('tags', text)}
            placeholder="Enter tags separated by commas"
            maxLength={200}
          />
          <Text style={styles.helperText}>Example: digital art, portrait, abstract</Text>
        </View>

        {/* Category Selection */}
        <View style={styles.section}>
          <Text style={styles.sectionTitle}>Category</Text>
          <View style={styles.optionGrid}>
            {categories.map((category) => (
              <TouchableOpacity
                key={category.value}
                style={[
                  styles.optionButton,
                  formData.category === category.value && styles.optionButtonSelected,
                ]}
                onPress={() => updateFormField('category', category.value)}
              >
                <Text
                  style={[
                    styles.optionButtonText,
                    formData.category === category.value && styles.optionButtonTextSelected,
                  ]}
                >
                  {category.label}
                </Text>
              </TouchableOpacity>
            ))}
          </View>
        </View>

        {/* License Selection */}
        <View style={styles.section}>
          <Text style={styles.sectionTitle}>License</Text>
          <View style={styles.optionList}>
            {licenses.map((license) => (
              <TouchableOpacity
                key={license.value}
                style={[
                  styles.optionListItem,
                  formData.license === license.value && styles.optionListItemSelected,
                ]}
                onPress={() => updateFormField('license', license.value)}
              >
                <Text
                  style={[
                    styles.optionListText,
                    formData.license === license.value && styles.optionListTextSelected,
                  ]}
                >
                  {license.label}
                </Text>
                {formData.license === license.value && (
                  <Ionicons name="checkmark" size={20} color="#007AFF" />
                )}
              </TouchableOpacity>
            ))}
          </View>
        </View>

        {/* Visibility Selection */}
        <View style={styles.section}>
          <Text style={styles.sectionTitle}>Visibility</Text>
          <View style={styles.optionList}>
            {visibilityOptions.map((option) => (
              <TouchableOpacity
                key={option.value}
                style={[
                  styles.optionListItem,
                  formData.visibility === option.value && styles.optionListItemSelected,
                ]}
                onPress={() => updateFormField('visibility', option.value)}
              >
                <Text
                  style={[
                    styles.optionListText,
                    formData.visibility === option.value && styles.optionListTextSelected,
                  ]}
                >
                  {option.label}
                </Text>
                {formData.visibility === option.value && (
                  <Ionicons name="checkmark" size={20} color="#007AFF" />
                )}
              </TouchableOpacity>
            ))}
          </View>
        </View>

        {/* AI Generated Toggle */}
        <View style={styles.section}>
          <TouchableOpacity
            style={styles.toggleContainer}
            onPress={() => updateFormField('isAiGenerated', !formData.isAiGenerated)}
          >
            <View>
              <Text style={styles.toggleTitle}>AI Generated</Text>
              <Text style={styles.toggleSubtitle}>Check if this artwork was created using AI</Text>
            </View>
            <View
              style={[
                styles.toggle,
                formData.isAiGenerated && styles.toggleActive,
              ]}
            >
              {formData.isAiGenerated && (
                <Ionicons name="checkmark" size={16} color="#ffffff" />
              )}
            </View>
          </TouchableOpacity>
        </View>

        {/* Submit Button */}
        <View style={styles.submitSection}>
          <TouchableOpacity
            style={[styles.submitButton, uploading && styles.submitButtonDisabled]}
            onPress={handleSubmit}
            disabled={uploading}
          >
            {uploading ? (
              <ActivityIndicator size="small" color="#ffffff" />
            ) : (
              <Ionicons name="cloud-upload" size={20} color="#ffffff" />
            )}
            <Text style={styles.submitButtonText}>
              {uploading ? 'Uploading...' : 'Upload Artwork'}
            </Text>
          </TouchableOpacity>
        </View>
      </ScrollView>
    </SafeAreaView>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#f8f9fa',
  },
  scrollView: {
    flex: 1,
  },
  header: {
    padding: 20,
    paddingTop: 10,
  },
  headerTitle: {
    fontSize: 28,
    fontWeight: 'bold',
    color: '#2c3e50',
    marginBottom: 4,
  },
  headerSubtitle: {
    fontSize: 16,
    color: '#666',
  },
  section: {
    backgroundColor: '#ffffff',
    marginHorizontal: 16,
    marginBottom: 16,
    padding: 20,
    borderRadius: 12,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 4,
    elevation: 3,
  },
  sectionTitle: {
    fontSize: 18,
    fontWeight: '600',
    color: '#2c3e50',
    marginBottom: 12,
  },
  mediaSelector: {
    borderWidth: 2,
    borderColor: '#007AFF',
    borderStyle: 'dashed',
    borderRadius: 12,
    padding: 40,
    alignItems: 'center',
    backgroundColor: '#f8f9ff',
  },
  mediaSelectorText: {
    fontSize: 16,
    fontWeight: '600',
    color: '#007AFF',
    marginTop: 12,
  },
  mediaSelectorSubtext: {
    fontSize: 14,
    color: '#666',
    marginTop: 4,
  },
  mediaContainer: {
    flexDirection: 'row',
    alignItems: 'center',
    padding: 16,
    backgroundColor: '#f8f9fa',
    borderRadius: 12,
    gap: 12,
  },
  selectedImage: {
    width: 80,
    height: 80,
    borderRadius: 8,
  },
  mediaInfo: {
    flex: 1,
  },
  mediaName: {
    fontSize: 16,
    fontWeight: '600',
    color: '#2c3e50',
    marginBottom: 4,
  },
  mediaSize: {
    fontSize: 14,
    color: '#666',
  },
  changeMediaButton: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 4,
    paddingHorizontal: 12,
    paddingVertical: 8,
    backgroundColor: '#e3f2fd',
    borderRadius: 8,
  },
  changeMediaText: {
    color: '#007AFF',
    fontSize: 14,
    fontWeight: '600',
  },
  languageSelector: {
    flexDirection: 'row',
    gap: 8,
  },
  languageButton: {
    flex: 1,
    paddingVertical: 12,
    paddingHorizontal: 16,
    borderRadius: 8,
    backgroundColor: '#f8f9fa',
    alignItems: 'center',
  },
  languageButtonActive: {
    backgroundColor: '#007AFF',
  },
  languageButtonText: {
    fontSize: 14,
    fontWeight: '600',
    color: '#666',
  },
  languageButtonTextActive: {
    color: '#ffffff',
  },
  textInput: {
    borderWidth: 1,
    borderColor: '#ddd',
    borderRadius: 8,
    padding: 12,
    fontSize: 16,
    backgroundColor: '#ffffff',
  },
  textArea: {
    height: 100,
    textAlignVertical: 'top',
  },
  helperText: {
    fontSize: 12,
    color: '#666',
    marginTop: 6,
    fontStyle: 'italic',
  },
  optionGrid: {
    flexDirection: 'row',
    flexWrap: 'wrap',
    gap: 8,
  },
  optionButton: {
    paddingHorizontal: 16,
    paddingVertical: 10,
    borderRadius: 20,
    backgroundColor: '#f8f9fa',
    borderWidth: 1,
    borderColor: '#ddd',
  },
  optionButtonSelected: {
    backgroundColor: '#007AFF',
    borderColor: '#007AFF',
  },
  optionButtonText: {
    fontSize: 14,
    color: '#666',
    fontWeight: '500',
  },
  optionButtonTextSelected: {
    color: '#ffffff',
  },
  optionList: {
    gap: 8,
  },
  optionListItem: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    padding: 16,
    borderRadius: 8,
    backgroundColor: '#f8f9fa',
    borderWidth: 1,
    borderColor: '#ddd',
  },
  optionListItemSelected: {
    backgroundColor: '#e3f2fd',
    borderColor: '#007AFF',
  },
  optionListText: {
    fontSize: 14,
    color: '#2c3e50',
    flex: 1,
  },
  optionListTextSelected: {
    color: '#007AFF',
    fontWeight: '600',
  },
  toggleContainer: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    padding: 16,
    backgroundColor: '#f8f9fa',
    borderRadius: 12,
  },
  toggleTitle: {
    fontSize: 16,
    fontWeight: '600',
    color: '#2c3e50',
    marginBottom: 4,
  },
  toggleSubtitle: {
    fontSize: 14,
    color: '#666',
  },
  toggle: {
    width: 24,
    height: 24,
    borderRadius: 12,
    borderWidth: 2,
    borderColor: '#ddd',
    backgroundColor: '#ffffff',
    justifyContent: 'center',
    alignItems: 'center',
  },
  toggleActive: {
    backgroundColor: '#007AFF',
    borderColor: '#007AFF',
  },
  submitSection: {
    paddingHorizontal: 16,
    paddingBottom: 40,
  },
  submitButton: {
    flexDirection: 'row',
    justifyContent: 'center',
    alignItems: 'center',
    gap: 8,
    backgroundColor: '#007AFF',
    paddingVertical: 16,
    borderRadius: 12,
    shadowColor: '#007AFF',
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.3,
    shadowRadius: 8,
    elevation: 5,
  },
  submitButtonDisabled: {
    backgroundColor: '#999',
    shadowOpacity: 0,
    elevation: 0,
  },
  submitButtonText: {
    color: '#ffffff',
    fontSize: 18,
    fontWeight: 'bold',
  },
});

export default UploadScreen;
