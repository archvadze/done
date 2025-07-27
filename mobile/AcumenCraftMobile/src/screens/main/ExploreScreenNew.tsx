import React, { useState, useEffect } from 'react';
import {
  View,
  StyleSheet,
  SafeAreaView,
  ScrollView,
  TouchableOpacity,
  Image,
  TextInput,
  FlatList,
  ActivityIndicator,
  RefreshControl,
  Alert,
  Dimensions,
} from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { useNavigation } from '@react-navigation/native';
import { StackNavigationProp } from '@react-navigation/stack';
import { ArtworkService } from '../../services/api/artworkService';
import { useAuth } from '../../context/AuthContext';
import { useKartTheme } from '../../contexts/KartThemeContext';
import { Artwork, RootStackParamList } from '../../types';

// ქართული კომპონენტები
import {
  KartTeqsti,
  KartKarti,
  KartGhilaki
} from '../../components/common';

type ExploreScreenNavigationProp = StackNavigationProp<RootStackParamList>;

const { width } = Dimensions.get('window');
const naxatis_sigane = (width - 60) / 2; // 2 სვეტი გამოშვებებით

const ExploreScreen: React.FC = () => {
  const navigation = useNavigation<ExploreScreenNavigationProp>();
  const { state } = useAuth();
  const theme = useKartTheme();

  const [namushevrebi, setNameshevrebi] = useState<Artwork[]>([]);
  const [gafiltrul_namushevrebi, setGafiltrul_namushevrebi] = useState<Artwork[]>([]);
  const [indebs, setIndebs] = useState(true);
  const [axlisGanaxleba, setAxlisGanaxleba] = useState(false);
  const [sapiovi_zapqhva, setSapzovi_zapqhva] = useState('');
  const [archevani_kategoria, setArchevani_kategoria] = useState<string>('qvela');
  const [gverdi, setGverdi] = useState(1);
  const [meti_aris, setMeti_aris] = useState(true);
  const [meti_indebs, setMeti_indebs] = useState(false);

  const artworkService = new ArtworkService();

  // კატეგორიები ფილტრაციისთვის
  const kategoriebi = [
    { id: 'qvela', dasaxeleba: 'ყველა', xatuli: '🎨' },
    { id: 'image', dasaxeleba: 'სურათები', xatuli: '🖼️' },
    { id: 'video', dasaxeleba: 'ვიდეოები', xatuli: '🎬' },
    { id: 'audio', dasaxeleba: 'აუდიო', xatuli: '🎵' },
    { id: 'ai', dasaxeleba: 'AI შექმნილი', xatuli: '🤖' },
  ];

  // ნამუშევრების ჩატვირთვა
  const namushevrebis_chatvirtva = async (gverdis_nomeri: number = 1, axlis_ganaxleba: boolean = false) => {
    try {
      if (axlis_ganaxleba) {
        setGverdi(1);
        setMeti_aris(true);
      }

      const response = await artworkService.getArtworks(gverdis_nomeri, 20);

      if (axlis_ganaxleba || gverdis_nomeri === 1) {
        setNameshevrebi(response.data);
      } else {
        setNameshevrebi(winapotuli => [...winapotuli, ...response.data]);
      }

      setMeti_aris(gverdis_nomeri < response.last_page);

    } catch (error) {
      console.error('Failed to load artworks:', error);
      Alert.alert('შეცდომა', 'ნამუშევრების ჩატვირთვა ვერ მოხერხდა');
    } finally {
      setIndebs(false);
      setAxlisGanaxleba(false);
      setMeti_indebs(false);
    }
  };

  // თავდაპირველი ჩატვირთვა
  useEffect(() => {
    namushevrebis_chatvirtva();
  }, []);

  // ნამუშევრების ფილტრაცია ძიებისა და კატეგორიის მიხედვით
  useEffect(() => {
    let gafiltrul = [...namushevrebi];

    // ძიების ფილტრი
    if (sapiovi_zapqhva.trim()) {
      gafiltrul = gafiltrul.filter(namushevari => {
        const satauri = naxatis_satauris_migeba(namushevari).toLowerCase();
        const tegebi = namushevari.tags?.toLowerCase() || '';
        const mxatvari = namushevari.user?.name?.toLowerCase() || '';
        const zapqhva = sapiovi_zapqhva.toLowerCase();

        return satauri.includes(zapqhva) || tegebi.includes(zapqhva) || mxatvari.includes(zapqhva);
      });
    }

    // კატეგორიის ფილტრი
    if (archevani_kategoria !== 'qvela') {
      if (archevani_kategoria === 'ai') {
        gafiltrul = gafiltrul.filter(namushevari => namushevari.is_ai_generated);
      } else {
        gafiltrul = gafiltrul.filter(namushevari => namushevari.media_type === archevani_kategoria);
      }
    }

    setGafiltrul_namushevrebi(gafiltrul);
  }, [namushevrebi, sapiovi_zapqhva, archevani_kategoria]);

  // ნამუშევრის სათაურის მიღება
  const naxatis_satauris_migeba = (namushevari: Artwork): string => {
    const sataurebi = namushevari.title;
    if (typeof sataurebi === 'string') return sataurebi;

    const momxmareblis_ena = state.user?.lang || 'ka';
    return sataurebi[momxmareblis_ena] || sataurebi.ka || sataurebi.en || sataurebi.de || 'უსახელო';
  };

  // განახლების მართვა
  const ganaxlebis_martva = () => {
    setAxlisGanaxleba(true);
    namushevrebis_chatvirtva(1, true);
  };

  // მეტის ჩატვირთვა
  const metis_chatvirtva = () => {
    if (!meti_indebs && meti_aris) {
      setMeti_indebs(true);
      const shemadeg_gverdi = gverdi + 1;
      setGverdi(shemadeg_gverdi);
      namushevrebis_chatvirtva(shemadeg_gverdi);
    }
  };

  // ნამუშევარზე დაკლიკება
  const namushevris_daklkeba = (namushevari: Artwork) => {
    navigation.navigate('ArtworkDetail', { artworkId: namushevari.id });
  };

  // ნამუშევრის რენდერი
  const namushevris_renderi = ({ item }: { item: Artwork }) => (
    <TouchableOpacity
      style={[styles.artworkItem, { width: naxatis_sigane }]}
      onPress={() => namushevris_daklkeba(item)}
    >
      <KartKarti
        varianti="naxati"
        stili={styles.artworkCard}
        shvilebi={
          <View>
            <Image
              source={{ uri: item.imageUrl }}
              style={styles.artworkImage}
              resizeMode="cover"
            />
            <View style={styles.artworkOverlay}>
              <View style={styles.artworkStats}>
                <View style={styles.statItem}>
                  <Ionicons name="heart" size={14} color={theme.colors.error[500]} />
                  <KartTeqsti
                    shvilebi={item.likesCount?.toString() || '0'}
                    varianti="dzalianPatara"
                    feri="white"
                    stili={{ marginLeft: 4 }}
                  />
                </View>
                <View style={styles.statItem}>
                  <Ionicons name="chatbubble" size={14} color={theme.colors.info[500]} />
                  <KartTeqsti
                    shvilebi={item.commentsCount?.toString() || '0'}
                    varianti="dzalianPatara"
                    feri="white"
                    stili={{ marginLeft: 4 }}
                  />
                </View>
              </View>
            </View>
            <View style={styles.artworkInfo}>
              <KartTeqsti
                shvilebi={naxatis_satauris_migeba(item)}
                varianti="patara"
                wona="sashualod"
                feri={theme.colors.text}
                raodenobisWyveta={1}
              />
              <KartTeqsti
                shvilebi={`${item.user?.name || 'უცნობი მხატვარი'}`}
                varianti="dzalianPatara"
                feri={theme.colors.textSecondary}
                stili={{ marginTop: 4 }}
              />
            </View>
          </View>
        }
      />
    </TouchableOpacity>
  );

  // კატეგორიის რენდერი
  const kategoriis_renderi = (kategoria: any) => (
    <TouchableOpacity
      key={kategoria.id}
      style={[
        styles.categoryButton,
        {
          backgroundColor: archevani_kategoria === kategoria.id
            ? theme.colors.gold[600]
            : theme.colors.surface,
          borderColor: archevani_kategoria === kategoria.id
            ? theme.colors.gold[600]
            : theme.colors.border,
        }
      ]}
      onPress={() => setArchevani_kategoria(kategoria.id)}
    >
      <KartTeqsti
        shvilebi={`${kategoria.xatuli} ${kategoria.dasaxeleba}`}
        varianti="patara"
        feri={archevani_kategoria === kategoria.id ? 'white' : theme.colors.text}
      />
    </TouchableOpacity>
  );

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
      {/* ზედა ნაწილი - სათაური და ძიება */}
      <View style={[styles.header, { paddingHorizontal: theme.spacing.lg }]}>
        <KartTeqsti
          shvilebi="აღმოაჩინეთ ხელოვნება"
          varianti="satauri3"
          feri={theme.colors.gold[600]}
          stili={{ marginBottom: theme.spacing.md }}
        />

        <View style={[styles.searchContainer, { backgroundColor: theme.colors.surface, borderColor: theme.colors.border }]}>
          <Ionicons name="search" size={20} color={theme.colors.textSecondary} style={styles.searchIcon} />
          <TextInput
            style={[styles.searchInput, { color: theme.colors.text }]}
            placeholder="ძიება ნამუშევრებში..."
            placeholderTextColor={theme.colors.textSecondary}
            value={sapiovi_zapqhva}
            onChangeText={setSapzovi_zapqhva}
          />
          {sapiovi_zapqhva ? (
            <TouchableOpacity onPress={() => setSapzovi_zapqhva('')}>
              <Ionicons name="close-circle" size={20} color={theme.colors.textSecondary} />
            </TouchableOpacity>
          ) : null}
        </View>
      </View>

      {/* კატეგორიები */}
      <ScrollView
        horizontal
        showsHorizontalScrollIndicator={false}
        style={styles.categoriesContainer}
        contentContainerStyle={{ paddingHorizontal: theme.spacing.lg }}
      >
        {kategoriebi.map(kategoria => kategoriis_renderi(kategoria))}
      </ScrollView>

      {/* შედეგების რაოდენობა */}
      <View style={[styles.resultsHeader, { paddingHorizontal: theme.spacing.lg }]}>
        <KartTeqsti
          shvilebi={`${gafiltrul_namushevrebi.length} ნამუშევარი ნაპოვნი`}
          varianti="paragraphi"
          feri={theme.colors.textSecondary}
        />
      </View>

      {/* ნამუშევრების ბადე */}
      <FlatList
        data={gafiltrul_namushevrebi}
        renderItem={namushevris_renderi}
        keyExtractor={(item) => item.id.toString()}
        numColumns={2}
        style={styles.artworksList}
        contentContainerStyle={{ paddingHorizontal: theme.spacing.lg }}
        showsVerticalScrollIndicator={false}
        refreshControl={
          <RefreshControl
            refreshing={axlisGanaxleba}
            onRefresh={ganaxlebis_martva}
            tintColor={theme.colors.gold[600]}
            colors={[theme.colors.gold[600]]}
          />
        }
        onEndReached={metis_chatvirtva}
        onEndReachedThreshold={0.1}
        ListFooterComponent={
          meti_indebs ? (
            <View style={styles.loadingMore}>
              <ActivityIndicator size="small" color={theme.colors.gold[600]} />
              <KartTeqsti
                shvilebi="მეტი იტვირთება..."
                varianti="patara"
                feri={theme.colors.textSecondary}
                stili={{ marginTop: 8 }}
              />
            </View>
          ) : null
        }
        ListEmptyComponent={
          <View style={styles.emptyContainer}>
            <Ionicons name="images-outline" size={64} color={theme.colors.textSecondary} />
            <KartTeqsti
              shvilebi="ნამუშევრები ვერ მოიძებნა"
              varianti="satauri5"
              feri={theme.colors.textSecondary}
              stili={{ marginTop: theme.spacing.md }}
              teqstisGaswporeba="centri"
            />
            <KartTeqsti
              shvilebi="სცადეთ სხვა საძიებო ტერმინი ან კატეგორია"
              varianti="paragraphi"
              feri={theme.colors.textSecondary}
              stili={{ marginTop: theme.spacing.sm, textAlign: 'center' }}
              teqstisGaswporeba="centri"
            />
          </View>
        }
      />
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
  header: {
    paddingTop: 20,
    paddingBottom: 16,
  },
  searchContainer: {
    flexDirection: 'row',
    alignItems: 'center',
    borderWidth: 1,
    borderRadius: 12,
    paddingHorizontal: 16,
    paddingVertical: 12,
  },
  searchIcon: {
    marginRight: 12,
  },
  searchInput: {
    flex: 1,
    fontSize: 16,
  },
  categoriesContainer: {
    marginBottom: 16,
  },
  categoryButton: {
    paddingHorizontal: 16,
    paddingVertical: 8,
    borderRadius: 20,
    borderWidth: 1,
    marginRight: 12,
  },
  resultsHeader: {
    paddingVertical: 8,
    marginBottom: 16,
  },
  artworksList: {
    flex: 1,
  },
  artworkItem: {
    marginBottom: 20,
    marginHorizontal: 8,
  },
  artworkCard: {
    padding: 0,
    overflow: 'hidden',
  },
  artworkImage: {
    width: '100%',
    height: 180,
  },
  artworkOverlay: {
    position: 'absolute',
    top: 0,
    left: 0,
    right: 0,
    bottom: 0,
    justifyContent: 'flex-end',
    alignItems: 'flex-end',
    padding: 12,
  },
  artworkStats: {
    flexDirection: 'row',
    backgroundColor: 'rgba(0,0,0,0.6)',
    borderRadius: 12,
    paddingHorizontal: 8,
    paddingVertical: 4,
  },
  statItem: {
    flexDirection: 'row',
    alignItems: 'center',
    marginLeft: 8,
  },
  artworkInfo: {
    padding: 12,
  },
  loadingMore: {
    paddingVertical: 20,
    alignItems: 'center',
  },
  emptyContainer: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
    paddingVertical: 60,
  },
});

export default ExploreScreen;
