import React, { useState, useEffect } from 'react';
import {
  View,
  StyleSheet,
  SafeAreaView,
  ScrollView,
  TouchableOpacity,
  Image,
  ActivityIndicator,
  RefreshControl,
  Alert,
  Dimensions,
} from 'react-native';
import { useAuth } from '../../context/AuthContext';
import { useKartTheme } from '../../contexts/KartThemeContext';
import { ArtworkService } from '../../services/api/artworkService';
import { Artwork } from '../../types';
import { Ionicons } from '@expo/vector-icons';
import { useNavigation } from '@react-navigation/native';
import { StackNavigationProp } from '@react-navigation/stack';
import { MainTabParamList } from '../../types';

// ქართული კომპონენტები
import {
  KartTeqsti,
  KartKarti,
  KartGhilaki
} from '../../components/common';

const { width } = Dimensions.get('window');
type HomeScreenProp = StackNavigationProp<MainTabParamList, 'Home'>;

const HomeScreen: React.FC = () => {
  const { state } = useAuth();
  const theme = useKartTheme();
  const navigation = useNavigation<HomeScreenProp>();

  const [rekomendebuli_naxatebi, setRekomendebuli_naxatebi] = useState<Artwork[]>([]);
  const [indebs, setIndebs] = useState(true);
  const [axlisGanaxleba, setAxlisGanaxleba] = useState(false);
  const artworkService = new ArtworkService();

  // ბოლო აქტივობების ქართული მონაცემები
  const bolo_aqtivoba = [
    {
      id: 1,
      tipi: 'shefaseba',
      shetyobineba: 'თქვენი ნამუშევარი "ზაფხულის ფერები" შეფასდა',
      dro: '2 საათის წინ',
      xatulis_saxeli: 'star' as keyof typeof Ionicons.glyphMap,
      feri: theme.colors.gold[500],
    },
    {
      id: 2,
      tipi: 'komentari',
      shetyobineba: 'მარიამმა კომენტარი დატოვა "მთის ხედზე"',
      dro: '5 საათის წინ',
      xatulis_saxeli: 'chatbubble' as keyof typeof Ionicons.glyphMap,
      feri: theme.colors.info[500],
    },
    {
      id: 3,
      tipi: 'motseba',
      shetyobineba: '3 ახალი მოწონება თქვენს ნამუშევარზე',
      dro: '1 დღის წინ',
      xatulis_saxeli: 'heart' as keyof typeof Ionicons.glyphMap,
      feri: theme.colors.error[500],
    },
  ];

  // რეკომენდებული ნახატების ჩატვირთვა
  const rekomendebuli_naxatebis_chatvirtva = async () => {
    try {
      const response = await artworkService.getArtworks(1, 6);
      setRekomendebuli_naxatebi(response.data);
    } catch (error) {
      console.error('Failed to load artworks:', error);
      Alert.alert('შეცდომა', 'რეკომენდებული ნამუშევრების ჩატვირთვა ვერ მოხერხდა');
    } finally {
      setIndebs(false);
      setAxlisGanaxleba(false);
    }
  };

  // თავდაპირველი ჩატვირთვა
  useEffect(() => {
    rekomendebuli_naxatebis_chatvirtva();
  }, []);

  // Pull to refresh
  const ganaxlebisGamortva = () => {
    setAxlisGanaxleba(true);
    rekomendebuli_naxatebis_chatvirtva();
  };

  // ნამუშევრის სათაურის მიღება
  const naxatis_satauris_migeba = (naxati: Artwork): string => {
    const sataurebi = naxati.title;
    if (typeof sataurebi === 'string') return sataurebi;

    // ქართული ენის პრიორიტეტი
    const momxmareblis_ena = state.user?.lang || 'ka';
    return sataurebi[momxmareblis_ena] || sataurebi.ka || sataurebi.en || sataurebi.de || 'უსახელო';
  };

  if (indebs) {
    return (
      <SafeAreaView style={[styles.container, { backgroundColor: theme.colors.background }]}>
        <View style={styles.loadingContainer}>
          <ActivityIndicator size="large" color={theme.colors.gold[600]} />
          <KartTeqsti
            shvilebi="ნამუშევრები იტვირთება..."
            varianti="paragraphi"
            feri={theme.colors.gold[500]}
            stili={{ marginTop: theme.spacing.md }}
          />
        </View>
      </SafeAreaView>
    );
  }

  return (
    <SafeAreaView style={[styles.container, { backgroundColor: theme.colors.background }]}>
      <ScrollView
        style={styles.scrollView}
        showsVerticalScrollIndicator={false}
        refreshControl={
          <RefreshControl
            refreshing={axlisGanaxleba}
            onRefresh={ganaxlebisGamortva}
            tintColor={theme.colors.gold[600]}
            colors={[theme.colors.gold[600]]}
          />
        }
      >
        {/* მისალმების სექცია */}
        <View style={[styles.header, { paddingHorizontal: theme.spacing.lg }]}>
          <KartTeqsti
            shvilebi={`მოგესალმებით, ${state.user?.firstName || 'მხატვარო'}!`}
            varianti="satauri5"
            feri={theme.colors.gold[500]}
          />
          <KartTeqsti
            shvilebi="აღმოაჩინეთ ქართული ხელოვნება"
            varianti="satauri2"
            feri={theme.colors.gold[700]}
            stili={{ marginTop: theme.spacing.xs }}
          />
        </View>

        {/* სტატისტიკის კარტები */}
        <View style={[styles.statsContainer, { paddingHorizontal: theme.spacing.lg }]}>
          <KartKarti
            varianti="standartuli"
            stili={styles.statCard}
            shvilebi={
              <View style={styles.statContent}>
                <KartTeqsti
                  shvilebi="42"
                  varianti="satauri3"
                  feri={theme.colors.gold[600]}
                  teqstisGaswporeba="centri"
                />
                <KartTeqsti
                  shvilebi="ნამუშევრები"
                  varianti="patara"
                  feri={theme.colors.textSecondary}
                  teqstisGaswporeba="centri"
                />
              </View>
            }
          />

          <KartKarti
            varianti="standartuli"
            stili={styles.statCard}
            shvilebi={
              <View style={styles.statContent}>
                <KartTeqsti
                  shvilebi="8.7"
                  varianti="satauri3"
                  feri={theme.colors.gold[600]}
                  teqstisGaswporeba="centri"
                />
                <KartTeqsti
                  shvilebi="შეფასება"
                  varianti="patara"
                  feri={theme.colors.textSecondary}
                  teqstisGaswporება="centri"
                />
              </View>
            }
          />

          <KartKarti
            varianti="standartuli"
            stili={styles.statCard}
            shvilebi={
              <View style={styles.statContent}>
                <KartTeqsti
                  shvilebi="156"
                  varianti="satauri3"
                  feri={theme.colors.gold[600]}
                  teqstisGaswporeba="centri"
                />
                <KartTeqsti
                  shvilebi="მოწონება"
                  varianti="patara"
                  feri={theme.colors.textSecondary}
                  teqstisGaswporeba="centri"
                />
              </View>
            }
          />
        </View>

        {/* ბოლო აქტივობები */}
        <View style={[styles.section, { paddingHorizontal: theme.spacing.lg }]}>
          <KartTeqsti
            shvilebi="ბოლო აქტივობები"
            varianti="satauri4"
            feri={theme.colors.gold[600]}
            stili={{ marginBottom: theme.spacing.md }}
          />

          {bolo_aqtivoba.map((aqtivoba) => (
            <KartKarti
              key={aqtivoba.id}
              varianti="standartuli"
              stili={styles.activityCard}
              shvilebi={
                <View style={styles.activityContent}>
                  <View style={[styles.activityIcon, { backgroundColor: aqtivoba.feri + '20' }]}>
                    <Ionicons
                      name={aqtivoba.xatulis_saxeli}
                      size={theme.iconSizes.md}
                      color={aqtivoba.feri}
                    />
                  </View>
                  <View style={styles.activityText}>
                    <KartTeqsti
                      shvilebi={aqtivoba.shetyobineba}
                      varianti="paragraphi"
                      feri={theme.colors.text}
                      raodenobisWyveta={2}
                    />
                    <KartTeqsti
                      shvilebi={aqtivoba.dro}
                      varianti="dzalianPatara"
                      feri={theme.colors.textSecondary}
                      stili={{ marginTop: theme.spacing.xs }}
                    />
                  </View>
                </View>
              }
            />
          ))}
        </View>

        {/* რეკომენდებული ნამუშევრები */}
        <View style={[styles.section, { paddingHorizontal: theme.spacing.lg }]}>
          <View style={styles.sectionHeader}>
            <KartTeqsti
              shvilebi="რეკომენდებული ნამუშევრები"
              varianti="satauri4"
              feri={theme.colors.gold[600]}
            />
            <TouchableOpacity onPress={() => navigation.navigate('Explore')}>
              <KartTeqsti
                shvilebi="ყველა ნახვა"
                varianti="patara"
                feri={theme.colors.gold[500]}
              />
            </TouchableOpacity>
          </View>

          <ScrollView
            horizontal
            showsHorizontalScrollIndicator={false}
            style={styles.artworkScroll}
          >
            {rekomendebuli_naxatebi.map((naxati) => (
              <KartKarti
                key={naxati.id}
                varianti="naxati"
                stili={styles.artworkCard}
                daaklaki={() => {
                  // Navigate to artwork detail
                  console.log('Navigate to artwork:', naxati.id);
                }}
                shvilebi={
                  <View>
                    <Image
                      source={{ uri: naxati.imageUrl }}
                      style={styles.artworkImage}
                      resizeMode="cover"
                    />
                    <View style={styles.artworkInfo}>
                      <KartTeqsti
                        shvilebi={naxatis_satauris_migeba(naxati)}
                        varianti="paragraphi"
                        wona="sashualod"
                        feri={theme.colors.text}
                        raodenobisWyveta={1}
                      />
                      <KartTeqsti
                        shvilebi={`${naxati.artist?.name || 'უცნობი მხატვარი'}`}
                        varianti="patara"
                        feri={theme.colors.textSecondary}
                        stili={{ marginTop: theme.spacing.xs }}
                      />
                      <View style={styles.artworkStats}>
                        <View style={styles.statItem}>
                          <Ionicons name="heart" size={14} color={theme.colors.error[500]} />
                          <KartTeqsti
                            shvilebi={naxati.likesCount?.toString() || '0'}
                            varianti="dzalianPatara"
                            feri={theme.colors.textSecondary}
                            stili={{ marginLeft: 4 }}
                          />
                        </View>
                        <View style={styles.statItem}>
                          <Ionicons name="chatbubble" size={14} color={theme.colors.info[500]} />
                          <KartTeqsti
                            shvilebi={naxati.commentsCount?.toString() || '0'}
                            varianti="dzalianPatara"
                            feri={theme.colors.textSecondary}
                            stili={{ marginLeft: 4 }}
                          />
                        </View>
                      </View>
                    </View>
                  </View>
                }
              />
            ))}
          </ScrollView>
        </View>

        {/* Quick Actions */}
        <View style={[styles.section, { paddingHorizontal: theme.spacing.lg, paddingBottom: theme.spacing.xl }]}>
          <KartTeqsti
            shvilebi="სწრაფი მოქმედებები"
            varianti="satauri4"
            feri={theme.colors.gold[600]}
            stili={{ marginBottom: theme.spacing.md }}
          />

          <View style={styles.quickActions}>
            <KartGhilaki
              satauri="ნამუშევრის ატვირთვა"
              varianti="dziritadi"
              zoma="sashualod"
              stili={styles.actionButton}
              daaklaki={() => navigation.navigate('Upload')}
            />
            <KartGhilaki
              satauri="აღმოჩენა"
              varianti="meoardi"
              zoma="sashualod"
              stili={styles.actionButton}
              daaklaki={() => navigation.navigate('Explore')}
            />
          </View>
        </View>
      </ScrollView>
    </SafeAreaView>
  );
};

// ქართული სტილები
const styles = StyleSheet.create({
  container: {
    flex: 1,
  },
  loadingContainer: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
  },
  scrollView: {
    flex: 1,
  },
  header: {
    paddingTop: 20,
    paddingBottom: 24,
  },
  statsContainer: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    marginBottom: 32,
  },
  statCard: {
    flex: 1,
    marginHorizontal: 4,
    paddingVertical: 16,
  },
  statContent: {
    alignItems: 'center',
  },
  section: {
    marginBottom: 32,
  },
  sectionHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: 16,
  },
  activityCard: {
    marginBottom: 12,
  },
  activityContent: {
    flexDirection: 'row',
    alignItems: 'flex-start',
  },
  activityIcon: {
    width: 40,
    height: 40,
    borderRadius: 20,
    justifyContent: 'center',
    alignItems: 'center',
    marginRight: 12,
  },
  activityText: {
    flex: 1,
  },
  artworkScroll: {
    marginLeft: -4,
  },
  artworkCard: {
    width: width * 0.6,
    marginLeft: 4,
    marginRight: 12,
  },
  artworkImage: {
    width: '100%',
    height: 140,
    borderRadius: 12,
    marginBottom: 12,
  },
  artworkInfo: {
    paddingHorizontal: 4,
  },
  artworkStats: {
    flexDirection: 'row',
    marginTop: 8,
  },
  statItem: {
    flexDirection: 'row',
    alignItems: 'center',
    marginRight: 12,
  },
  quickActions: {
    flexDirection: 'row',
    justifyContent: 'space-between',
  },
  actionButton: {
    flex: 1,
    marginHorizontal: 4,
  },
});

export default HomeScreen;
