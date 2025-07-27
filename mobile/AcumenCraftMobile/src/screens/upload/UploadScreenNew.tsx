import React, { useState, useEffect } from 'react';
import {
  View,
  StyleSheet,
  SafeAreaView,
  ScrollView,
  TouchableOpacity,
  TextInput,
  Image,
  Alert,
  ActivityIndicator,
  Dimensions,
  Switch,
} from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import * as ImagePicker from 'expo-image-picker';
import * as DocumentPicker from 'expo-document-picker';
import { useAuth } from '../../context/AuthContext';
import { useKartTheme } from '../../contexts/KartThemeContext';
import { ArtworkService } from '../../services/api/artworkService';
import { useNavigation } from '@react-navigation/native';
import { StackNavigationProp } from '@react-navigation/stack';
import { MainTabParamList } from '../../types';

// ქართული კომპონენტები
import {
  KartTeqsti,
  KartKarti,
  KartGhilaki,
  KartSheyvana
} from '../../components/common';

type UploadNavigationProp = StackNavigationProp<MainTabParamList, 'Upload'>;

const { width } = Dimensions.get('window');

interface UploadFormData {
  satauri: {
    en: string;
    ka: string;
    de: string;
  };
  aghwera: {
    en: string;
    ka: string;
    de: string;
  };
  tegebi: string;
  licenzia: string;
  xelmiswvdomoba: string;
  kategoria: string;
  ai_shqmniliao: boolean;
}

const UploadScreen: React.FC = () => {
  const navigation = useNavigation<UploadNavigationProp>();
  const { state } = useAuth();
  const theme = useKartTheme();

  const [archevani_media, setArchevani_media] = useState<any>(null);
  const [itvirtveba, setItvirtveba] = useState(false);
  const [aqtiuri_ena, setAqtiuri_ena] = useState<'en' | 'ka' | 'de'>('ka');

  const [formis_monacemebi, setFormis_monacemebi] = useState<UploadFormData>({
    satauri: {
      en: '',
      ka: '',
      de: '',
    },
    aghwera: {
      en: '',
      ka: '',
      de: '',
    },
    tegebi: '',
    licenzia: 'CC BY-SA',
    xelmiswvdomoba: 'sazogadoebrivi',
    kategoria: 'sxva',
    ai_shqmniliao: false,
  });

  const artworkService = new ArtworkService();

  // ნებართვების მოთხოვნა
  useEffect(() => {
    nebartwebis_motxovna();
  }, []);

  const nebartwebis_motxovna = async () => {
    const { status: cameraStatus } = await ImagePicker.requestCameraPermissionsAsync();
    const { status: libraryStatus } = await ImagePicker.requestMediaLibraryPermissionsAsync();

    if (cameraStatus !== 'granted' || libraryStatus !== 'granted') {
      Alert.alert(
        'ნებართვები საჭიროა',
        'კამერისა და ფოტო ბიბლიოთეკის წვდომა საჭიროა ნამუშევრის ასატვირთად.',
        [{ text: 'კარგი' }]
      );
    }
  };

  // მედია არჩევანების ჩვენება
  const medias_archevnebis_chveneba = () => {
    Alert.alert(
      'მედიის არჩევა',
      'აირჩიეთ როგორ გსურთ თქვენი ნამუშევრის დამატება',
      [
        { text: 'კამერა', onPress: kameras_gaxsna },
        { text: 'ფოტო ბიბლიოთეკა', onPress: suratis_archevnis_gaxsna },
        { text: 'ფაილ ბრაუზერი', onPress: dokumentis_archevnis_gaxsna },
        { text: 'გაუქმება', style: 'cancel' },
      ]
    );
  };

  // კამერის გახსნა
  const kameras_gaxsna = async () => {
    try {
      const result = await ImagePicker.launchCameraAsync({
        mediaTypes: ImagePicker.MediaTypeOptions.All,
        allowsEditing: true,
        aspect: [1, 1],
        quality: 0.8,
      });

      if (!result.canceled && result.assets[0]) {
        setArchevani_media(result.assets[0]);
      }
    } catch (error) {
      console.error('Error opening camera:', error);
      Alert.alert('შეცდომა', 'კამერის გახსნა ვერ მოხერხდა');
    }
  };

  // სურათის არჩევნის გახსნა
  const suratis_archevnis_gaxsna = async () => {
    try {
      const result = await ImagePicker.launchImageLibraryAsync({
        mediaTypes: ImagePicker.MediaTypeOptions.All,
        allowsEditing: true,
        aspect: [1, 1],
        quality: 0.8,
      });

      if (!result.canceled && result.assets[0]) {
        setArchevani_media(result.assets[0]);
      }
    } catch (error) {
      console.error('Error opening image picker:', error);
      Alert.alert('შეცდომა', 'ფოტო ბიბლიოთეკის გახსნა ვერ მოხერხდა');
    }
  };

  // დოკუმენტის არჩევნის გახსნა
  const dokumentis_archevnis_gaxsna = async () => {
    try {
      const result = await DocumentPicker.getDocumentAsync({
        type: ['image/*', 'video/*', 'audio/*'],
        copyToCacheDirectory: true,
      });

      if (!result.canceled && result.assets[0]) {
        setArchevani_media(result.assets[0]);
      }
    } catch (error) {
      console.error('Error opening document picker:', error);
      Alert.alert('შეცდომა', 'ფაილ ბრაუზერის გახსნა ვერ მოხერხდა');
    }
  };

  // ფორმის ველის განახლება
  const formis_velis_ganaxleba = (veli: keyof UploadFormData, mnisvneloba: any) => {
    setFormis_monacemebi(winapotuli => ({
      ...winapotuli,
      [veli]: mnisvneloba,
    }));
  };

  // ენობრივი ფილდის განახლება
  const enobrivoi_vilis_ganaxleba = (veli: 'satauri' | 'aghwera', ena: 'en' | 'ka' | 'de', mnisvneloba: string) => {
    setFormis_monacemebi(winapotuli => ({
      ...winapotuli,
      [veli]: {
        ...winapotuli[veli],
        [ena]: mnisvneloba,
      },
    }));
  };

  // ნამუშევრის ატვირთვა
  const namushevris_atvirtva = async () => {
    // ვალიდაცია
    if (!archevani_media) {
      Alert.alert('შეცდომა', 'გთხოვთ აირჩიოთ ნამუშევარი');
      return;
    }

    if (!formis_monacemebi.satauri.ka.trim()) {
      Alert.alert('შეცდომა', 'გთხოვთ შეიყვანოთ ქართული სათაური');
      return;
    }

    setItvirtveba(true);

    try {
      // ატვირთვის მონაცემების მომზადება
      const uploadData = new FormData();

      // ფაილის დამატება
      uploadData.append('artwork', {
        uri: archevani_media.uri,
        type: archevani_media.type || 'image/jpeg',
        name: archevani_media.name || 'artwork.jpg',
      } as any);

      // მეტა მონაცემების დამატება
      uploadData.append('title', JSON.stringify(formis_monacemebi.satauri));
      uploadData.append('description', JSON.stringify(formis_monacemebi.aghwera));
      uploadData.append('tags', formis_monacemebi.tegebi);
      uploadData.append('license', formis_monacemebi.licenzia);
      uploadData.append('visibility', formis_monacemebi.xelmiswvdomoba);
      uploadData.append('category', formis_monacemebi.kategoria);
      uploadData.append('is_ai_generated', formis_monacemebi.ai_shqmniliao.toString());

      // ატვირთვა
      await artworkService.uploadArtwork(uploadData);

      Alert.alert(
        'წარმატება',
        'ნამუშევარი წარმატებით აიტვირთა',
        [
          {
            text: 'კარგი',
            onPress: () => {
              navigation.goBack();
            },
          },
        ]
      );
    } catch (error) {
      console.error('Upload error:', error);
      Alert.alert('შეცდომა', 'ნამუშევრის ატვირთვა ვერ მოხერხდა');
    } finally {
      setItvirtveba(false);
    }
  };

  // ენის ტაბების რენდერი
  const enis_tabebis_renderi = () => (
    <View style={styles.languageTabs}>
      {[
        { kod: 'ka', dasaxeleba: 'ქართული' },
        { kod: 'en', dasaxeleba: 'English' },
        { kod: 'de', dasaxeleba: 'Deutsch' },
      ].map((ena) => (
        <TouchableOpacity
          key={ena.kod}
          style={[
            styles.languageTab,
            {
              backgroundColor: aqtiuri_ena === ena.kod
                ? theme.colors.gold[600]
                : theme.colors.surface,
              borderColor: aqtiuri_ena === ena.kod
                ? theme.colors.gold[600]
                : theme.colors.border,
            }
          ]}
          onPress={() => setAqtiuri_ena(ena.kod as 'en' | 'ka' | 'de')}
        >
          <KartTeqsti
            shvilebi={ena.dasaxeleba}
            varianti="patara"
            feri={aqtiuri_ena === ena.kod ? 'white' : theme.colors.text}
          />
        </TouchableOpacity>
      ))}
    </View>
  );

  return (
    <SafeAreaView style={[styles.container, { backgroundColor: theme.colors.background }]}>
      {/* ზედა ნავიგაცია */}
      <View style={[styles.header, { paddingHorizontal: theme.spacing.lg }]}>
        <TouchableOpacity onPress={() => navigation.goBack()}>
          <Ionicons name="arrow-back" size={24} color={theme.colors.text} />
        </TouchableOpacity>
        <KartTeqsti
          shvilebi="ნამუშევრის ატვირთვა"
          varianti="satauri4"
          feri={theme.colors.text}
        />
        <View style={{ width: 24 }} />
      </View>

      <ScrollView style={styles.scrollView} showsVerticalScrollIndicator={false}>
        {/* მედია ასარჩევი */}
        <KartKarti
          varianti="standartuli"
          stili={[styles.mediaSelector, { marginHorizontal: theme.spacing.lg }]}
          shvilebi={
            <View>
              {archevani_media ? (
                <View style={styles.selectedMediaContainer}>
                  <Image source={{ uri: archevani_media.uri }} style={styles.selectedMedia} />
                  <TouchableOpacity
                    style={[styles.changeMediaButton, { backgroundColor: theme.colors.gold[600] }]}
                    onPress={medias_archevnebis_chveneba}
                  >
                    <Ionicons name="camera" size={20} color="white" />
                    <KartTeqsti
                      shvilebi="შეცვლა"
                      varianti="patara"
                      feri="white"
                      stili={{ marginLeft: 8 }}
                    />
                  </TouchableOpacity>
                </View>
              ) : (
                <TouchableOpacity style={styles.mediaPlaceholder} onPress={medias_archevnebis_chveneba}>
                  <Ionicons name="camera-outline" size={48} color={theme.colors.textSecondary} />
                  <KartTeqsti
                    shvilebi="ნამუშევრის არჩევა"
                    varianti="satauri5"
                    feri={theme.colors.textSecondary}
                    stili={{ marginTop: theme.spacing.sm }}
                  />
                  <KartTeqsti
                    shvilebi="დააკლიკეთ ფოტოს, ვიდეოს ან აუდიო ფაილის ასარჩევად"
                    varianti="paragraphi"
                    feri={theme.colors.textSecondary}
                    stili={{ marginTop: theme.spacing.xs }}
                    teqstisGaswporeba="centri"
                  />
                </TouchableOpacity>
              )}
            </View>
          }
        />

        {/* ფორმის ველები */}
        <View style={[styles.formContainer, { marginHorizontal: theme.spacing.lg }]}>
          {/* ენის ტაბები */}
          {enis_tabebis_renderi()}

          {/* სათაური */}
          <KartSheyvana
            satauri="სათაური"
            mnisvneloba={formis_monacemebi.satauri[aqtiuri_ena]}
            shesacvlelia={true}
            varianti="standartuli"
            mtavariVeli={true}
            valinaciasTeksti="სათაური აუცილებელია"
            chamonatvali={{}}
            onChangeText={(teqsti) => enobrivoi_vilis_ganaxleba('satauri', aqtiuri_ena, teqsti)}
            stili={{ marginBottom: theme.spacing.lg }}
          />

          {/* აღწერა */}
          <KartSheyvana
            satauri="აღწერა"
            mnisvneloba={formis_monacemebi.aghwera[aqtiuri_ena]}
            shesacvlelia={true}
            varianti="teqstisAreali"
            chamonatvali={{}}
            onChangeText={(teqsti) => enobrivoi_vilis_ganaxleba('aghwera', aqtiuri_ena, teqsti)}
            stili={{ marginBottom: theme.spacing.lg }}
          />

          {/* თეგები */}
          <KartSheyvana
            satauri="თეგები"
            mnisvneloba={formis_monacemebi.tegebi}
            shesacvlelia={true}
            varianti="standartuli"
            dawkoeba="მაგ: ხელოვნება, ნახატი, თანამედროვე"
            chamonatvali={{}}
            onChangeText={(teqsti) => formis_velis_ganaxleba('tegebi', teqsti)}
            stili={{ marginBottom: theme.spacing.lg }}
          />

          {/* კატეგორია */}
          <View style={{ marginBottom: theme.spacing.lg }}>
            <KartTeqsti
              shvilebi="კატეგორია"
              varianti="paragraphi"
              wona="sashualod"
              feri={theme.colors.text}
              stili={{ marginBottom: theme.spacing.sm }}
            />
            <View style={styles.categoryGrid}>
              {[
                { kod: 'naxati', dasaxeleba: 'ნახატი', xatuli: '🎨' },
                { kod: 'fotografia', dasaxeleba: 'ფოტოგრაფია', xatuli: '📸' },
                { kod: 'skulptura', dasaxeleba: 'სკულპტურა', xatuli: '🗿' },
                { kod: 'cifruli', dasaxeleba: 'ციფრული', xatuli: '💻' },
                { kod: 'txzuleba', dasaxeleba: 'ტექსტი', xatuli: '📝' },
                { kod: 'sxva', dasaxeleba: 'სხვა', xatuli: '🎭' },
              ].map((kategoria) => (
                <TouchableOpacity
                  key={kategoria.kod}
                  style={[
                    styles.categoryButton,
                    {
                      backgroundColor: formis_monacemebi.kategoria === kategoria.kod
                        ? theme.colors.gold[600]
                        : theme.colors.surface,
                      borderColor: formis_monacemebi.kategoria === kategoria.kod
                        ? theme.colors.gold[600]
                        : theme.colors.border,
                    }
                  ]}
                  onPress={() => formis_velis_ganaxleba('kategoria', kategoria.kod)}
                >
                  <KartTeqsti
                    shvilebi={`${kategoria.xatuli} ${kategoria.dasaxeleba}`}
                    varianti="patara"
                    feri={formis_monacemebi.kategoria === kategoria.kod ? 'white' : theme.colors.text}
                  />
                </TouchableOpacity>
              ))}
            </View>
          </View>

          {/* ხელმისაწვდომობა */}
          <View style={{ marginBottom: theme.spacing.lg }}>
            <KartTeqsti
              shvilebi="ხელმისაწვდომობა"
              varianti="paragraphi"
              wona="sashualod"
              feri={theme.colors.text}
              stili={{ marginBottom: theme.spacing.sm }}
            />
            <View style={styles.visibilityOptions}>
              {[
                { kod: 'sazogadoebrivi', dasaxeleba: 'საზოგადოებრივი', aghwera: 'ყველას შეუძლია ნახვა' },
                { kod: 'piradi', dasaxeleba: 'პირადი', aghwera: 'მხოლოდ თქვენ შეგიძლიათ ნახვა' },
              ].map((varianti) => (
                <TouchableOpacity
                  key={varianti.kod}
                  style={[
                    styles.visibilityOption,
                    {
                      backgroundColor: formis_monacemebi.xelmiswvdomoba === varianti.kod
                        ? theme.colors.gold[100]
                        : 'transparent',
                      borderColor: formis_monacemebi.xelmiswvdomoba === varianti.kod
                        ? theme.colors.gold[600]
                        : theme.colors.border,
                    }
                  ]}
                  onPress={() => formis_velis_ganaxleba('xelmiswvdomoba', varianti.kod)}
                >
                  <View style={styles.radioButton}>
                    {formis_monacemebi.xelmiswvdomoba === varianti.kod && (
                      <View style={[styles.radioSelected, { backgroundColor: theme.colors.gold[600] }]} />
                    )}
                  </View>
                  <View style={styles.optionText}>
                    <KartTeqsti
                      shvilebi={varianti.dasaxeleba}
                      varianti="paragraphi"
                      wona="sashualod"
                      feri={theme.colors.text}
                    />
                    <KartTeqsti
                      shvilebi={varianti.aghwera}
                      varianti="patara"
                      feri={theme.colors.textSecondary}
                    />
                  </View>
                </TouchableOpacity>
              ))}
            </View>
          </View>

          {/* AI შექმნილი */}
          <View style={[styles.switchContainer, { borderTopColor: theme.colors.border }]}>
            <View style={styles.switchText}>
              <KartTeqsti
                shvilebi="AI-ით შექმნილი"
                varianti="paragraphi"
                wona="sashualod"
                feri={theme.colors.text}
              />
              <KartTeqsti
                shvilebi="ნიშნავს რომ ეს ნამუშევარი AI-ით არის შექმნილი"
                varianti="patara"
                feri={theme.colors.textSecondary}
              />
            </View>
            <Switch
              value={formis_monacemebi.ai_shqmniliao}
              onValueChange={(mnisvneloba) => formis_velis_ganaxleba('ai_shqmniliao', mnisvneloba)}
              trackColor={{ false: theme.colors.border, true: theme.colors.gold[300] }}
              thumbColor={formis_monacemebi.ai_shqmniliao ? theme.colors.gold[600] : theme.colors.surface}
            />
          </View>
        </View>

        {/* ატვირთვის ღილაკი */}
        <View style={[styles.uploadButtonContainer, { paddingHorizontal: theme.spacing.lg }]}>
          <KartGhilaki
            satauri={itvirtveba ? 'იტვირთება...' : 'ნამუშევრის ატვირთვა'}
            varianti="dziritadi"
            zoma="didi"
            gauqmebuliao={itvirtveba || !archevani_media}
            stili={styles.uploadButton}
            daaklaki={namushevris_atvirtva}
          />
        </View>
      </ScrollView>

      {/* ატვირთვის ლოადერი */}
      {itvirtveba && (
        <View style={styles.uploadOverlay}>
          <View style={[styles.uploadModal, { backgroundColor: theme.colors.surface }]}>
            <ActivityIndicator size="large" color={theme.colors.gold[600]} />
            <KartTeqsti
              shvilebi="ნამუშევარი იტვირთება..."
              varianti="satauri5"
              feri={theme.colors.text}
              stili={{ marginTop: theme.spacing.md }}
            />
          </View>
        </View>
      )}
    </SafeAreaView>
  );
};

// ქართული სტილები
const styles = StyleSheet.create({
  container: {
    flex: 1,
  },
  header: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    paddingVertical: 16,
    borderBottomWidth: 1,
    borderBottomColor: 'rgba(0,0,0,0.1)',
  },
  scrollView: {
    flex: 1,
  },
  mediaSelector: {
    marginTop: 20,
    marginBottom: 24,
  },
  selectedMediaContainer: {
    alignItems: 'center',
  },
  selectedMedia: {
    width: width - 80,
    height: 200,
    borderRadius: 12,
    marginBottom: 16,
  },
  changeMediaButton: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingHorizontal: 16,
    paddingVertical: 8,
    borderRadius: 20,
  },
  mediaPlaceholder: {
    alignItems: 'center',
    paddingVertical: 60,
  },
  formContainer: {
    paddingBottom: 32,
  },
  languageTabs: {
    flexDirection: 'row',
    marginBottom: 24,
  },
  languageTab: {
    flex: 1,
    paddingVertical: 8,
    paddingHorizontal: 16,
    borderRadius: 8,
    borderWidth: 1,
    marginHorizontal: 4,
    alignItems: 'center',
  },
  categoryGrid: {
    flexDirection: 'row',
    flexWrap: 'wrap',
    marginHorizontal: -4,
  },
  categoryButton: {
    width: '48%',
    marginHorizontal: '1%',
    marginBottom: 12,
    paddingVertical: 12,
    paddingHorizontal: 16,
    borderRadius: 8,
    borderWidth: 1,
    alignItems: 'center',
  },
  visibilityOptions: {
    gap: 12,
  },
  visibilityOption: {
    flexDirection: 'row',
    alignItems: 'center',
    padding: 16,
    borderRadius: 8,
    borderWidth: 1,
  },
  radioButton: {
    width: 20,
    height: 20,
    borderRadius: 10,
    borderWidth: 2,
    borderColor: '#ccc',
    marginRight: 12,
    justifyContent: 'center',
    alignItems: 'center',
  },
  radioSelected: {
    width: 10,
    height: 10,
    borderRadius: 5,
  },
  optionText: {
    flex: 1,
  },
  switchContainer: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    paddingTop: 20,
    marginTop: 20,
    borderTopWidth: 1,
  },
  switchText: {
    flex: 1,
    marginRight: 16,
  },
  uploadButtonContainer: {
    paddingVertical: 24,
  },
  uploadButton: {
    width: '100%',
  },
  uploadOverlay: {
    position: 'absolute',
    top: 0,
    left: 0,
    right: 0,
    bottom: 0,
    backgroundColor: 'rgba(0,0,0,0.5)',
    justifyContent: 'center',
    alignItems: 'center',
  },
  uploadModal: {
    paddingHorizontal: 32,
    paddingVertical: 24,
    borderRadius: 12,
    alignItems: 'center',
  },
});

export default UploadScreen;
