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

// ქართული კომპონენტები
import {
  KartTeqsti,
  KartKarti,
  KartGhilaki
} from '../../components/common';

const { width } = Dimensions.get('window');

const HomeScreen: React.FC = () => {
  const { state } = useAuth();
  const theme = useKartTheme();
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
    },
    {
      id: 2,
      tipi: 'komentari',
      shetyobineba: 'მარიამმა კომენტარი დატოვა "მთის ხედზე"',
      dro: '5 საათის წინ',
      xatulis_saxeli: 'chatbubble' as keyof typeof Ionicons.glyphMap,
    },
    {
      id: 3,
      tipi: 'motseba',
      shetyobineba: '3 ახალი მოწონება თქვენს ნამუშევარზე',
      dro: '1 დღის წინ',
      xatulis_saxeli: 'heart' as keyof typeof Ionicons.glyphMap,
    },
  ];

  // რეკომენდებული ნახატების ჩატვირთვა
  const rekomendebuli_naxatebis_chatvirtva = async () => {
    try {
      const response = await artworkService.getArtworks(1, 6);
      setFeaturedArtworks(response.data);
    } catch (error) {
      console.error('Failed to load artworks:', error);
      Alert.alert('Error', 'Failed to load featured artworks');
    } finally {
      setLoading(false);
      setRefreshing(false);
    }
  };

  // Initial load
  useEffect(() => {
    loadFeaturedArtworks();
  }, []);

  // Pull to refresh
  const onRefresh = () => {
    setRefreshing(true);
    loadFeaturedArtworks();
  };

  // Get artwork title based on current language
  const getArtworkTitle = (artwork: Artwork): string => {
    const titles = artwork.title;
    if (typeof titles === 'string') return titles;

    // Try to get title in user's preferred language, fallback to English
    const userLang = state.user?.lang || 'en';
    return titles[userLang] || titles.en || titles.ka || titles.de || 'Untitled';
  };

  if (loading) {
    return (
      <SafeAreaView style={styles.container}>
        <View style={styles.loadingContainer}>
          <ActivityIndicator size="large" color="#007AFF" />
          <Text style={styles.loadingText}>Loading...</Text>
        </View>
      </SafeAreaView>
    );
  }

  return (
    <SafeAreaView style={styles.container}>
      <ScrollView
        style={styles.scrollView}
        refreshControl={
          <RefreshControl refreshing={refreshing} onRefresh={onRefresh} />
        }
      >
        {/* Header */}
        <View style={styles.header}>
          <Text style={styles.greeting}>
            Good morning{state.user?.name ? `, ${state.user.name}` : ''}!
          </Text>
          <Text style={styles.title}>Discover Amazing Art</Text>
        </View>

        {/* Quick Stats */}
        <View style={styles.statsContainer}>
          <View style={styles.statCard}>
            <Text style={styles.statNumber}>42</Text>
            <Text style={styles.statLabel}>Artworks</Text>
          </View>
          <View style={styles.statCard}>
            <Text style={styles.statNumber}>8.7</Text>
            <Text style={styles.statLabel}>Avg ACQ</Text>
          </View>
          <View style={styles.statCard}>
            <Text style={styles.statNumber}>156</Text>
            <Text style={styles.statLabel}>Followers</Text>
          </View>
        </View>

        {/* Featured Artworks */}
        <View style={styles.section}>
          <View style={styles.sectionHeader}>
            <Text style={styles.sectionTitle}>Featured Artworks</Text>
            <TouchableOpacity>
              <Text style={styles.seeAllText}>See All</Text>
            </TouchableOpacity>
          </View>

          <ScrollView horizontal showsHorizontalScrollIndicator={false}>
            {featuredArtworks.map((artwork) => (
              <TouchableOpacity key={artwork.id} style={styles.artworkCard}>
                <Image
                  source={{ uri: artwork.media_url || 'https://picsum.photos/300/200?random=' + artwork.id }}
                  style={styles.artworkImage}
                />
                <View style={styles.artworkInfo}>
                  <Text style={styles.artworkTitle}>{getArtworkTitle(artwork)}</Text>
                  <Text style={styles.artworkArtist}>by {artwork.user?.name || 'Unknown Artist'}</Text>
                  {artwork.acq_score && (
                    <View style={styles.acqBadge}>
                      <Text style={styles.acqScore}>ACQ {artwork.acq_score.toFixed(1)}</Text>
                    </View>
                  )}
                </View>
              </TouchableOpacity>
            ))}
          </ScrollView>
        </View>

        {/* Recent Activity */}
        <View style={styles.section}>
          <Text style={styles.sectionTitle}>Recent Activity</Text>

          {recentActivity.map((activity) => (
            <TouchableOpacity key={activity.id} style={styles.activityCard}>
              <View style={styles.activityIcon}>
                <Text style={styles.activityIconText}>
                  {activity.type === 'evaluation' ? '⭐' :
                    activity.type === 'comment' ? '💬' : '❤️'}
                </Text>
              </View>
              <View style={styles.activityContent}>
                <Text style={styles.activityMessage}>{activity.message}</Text>
                <Text style={styles.activityTime}>{activity.time}</Text>
              </View>
            </TouchableOpacity>
          ))}
        </View>

        {/* Quick Actions */}
        <View style={styles.section}>
          <Text style={styles.sectionTitle}>Quick Actions</Text>

          <View style={styles.quickActions}>
            <TouchableOpacity style={styles.actionButton}>
              <Text style={styles.actionIcon}>📸</Text>
              <Text style={styles.actionText}>Upload Artwork</Text>
            </TouchableOpacity>

            <TouchableOpacity style={styles.actionButton}>
              <Text style={styles.actionIcon}>🔍</Text>
              <Text style={styles.actionText}>Explore</Text>
            </TouchableOpacity>

            <TouchableOpacity style={styles.actionButton}>
              <Text style={styles.actionIcon}>👥</Text>
              <Text style={styles.actionText}>Communities</Text>
            </TouchableOpacity>

            <TouchableOpacity style={styles.actionButton}>
              <Text style={styles.actionIcon}>⚙️</Text>
              <Text style={styles.actionText}>Settings</Text>
            </TouchableOpacity>
          </View>
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
  loadingContainer: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
  },
  loadingText: {
    marginTop: 16,
    fontSize: 16,
    color: '#666666',
  },
  scrollView: {
    flex: 1,
  },
  header: {
    padding: 20,
    paddingTop: 10,
  },
  greeting: {
    fontSize: 16,
    color: '#666666',
    marginBottom: 4,
  },
  title: {
    fontSize: 28,
    fontWeight: 'bold',
    color: '#1a1a1a',
  },
  statsContainer: {
    flexDirection: 'row',
    paddingHorizontal: 20,
    marginBottom: 20,
    gap: 12,
  },
  statCard: {
    flex: 1,
    backgroundColor: '#ffffff',
    padding: 16,
    borderRadius: 12,
    alignItems: 'center',
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 4,
    elevation: 3,
  },
  statNumber: {
    fontSize: 24,
    fontWeight: 'bold',
    color: '#007AFF',
    marginBottom: 4,
  },
  statLabel: {
    fontSize: 14,
    color: '#666666',
  },
  section: {
    marginBottom: 24,
  },
  sectionHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    paddingHorizontal: 20,
    marginBottom: 12,
  },
  sectionTitle: {
    fontSize: 20,
    fontWeight: 'bold',
    color: '#1a1a1a',
    paddingHorizontal: 20,
    marginBottom: 12,
  },
  seeAllText: {
    color: '#007AFF',
    fontSize: 16,
  },
  artworkCard: {
    width: 200,
    marginLeft: 20,
    backgroundColor: '#ffffff',
    borderRadius: 12,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 4,
    elevation: 3,
  },
  artworkImage: {
    width: '100%',
    height: 120,
    borderTopLeftRadius: 12,
    borderTopRightRadius: 12,
  },
  artworkInfo: {
    padding: 12,
  },
  artworkTitle: {
    fontSize: 16,
    fontWeight: '600',
    color: '#1a1a1a',
    marginBottom: 4,
  },
  artworkArtist: {
    fontSize: 14,
    color: '#666666',
    marginBottom: 8,
  },
  acqBadge: {
    backgroundColor: '#007AFF',
    paddingHorizontal: 8,
    paddingVertical: 4,
    borderRadius: 12,
    alignSelf: 'flex-start',
  },
  acqScore: {
    color: '#ffffff',
    fontSize: 12,
    fontWeight: '600',
  },
  activityCard: {
    flexDirection: 'row',
    backgroundColor: '#ffffff',
    marginHorizontal: 20,
    marginBottom: 8,
    padding: 16,
    borderRadius: 12,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 1 },
    shadowOpacity: 0.1,
    shadowRadius: 2,
    elevation: 2,
  },
  activityIcon: {
    width: 40,
    height: 40,
    borderRadius: 20,
    backgroundColor: '#f0f0f0',
    justifyContent: 'center',
    alignItems: 'center',
    marginRight: 12,
  },
  activityIconText: {
    fontSize: 18,
  },
  activityContent: {
    flex: 1,
  },
  activityMessage: {
    fontSize: 16,
    color: '#1a1a1a',
    marginBottom: 4,
  },
  activityTime: {
    fontSize: 14,
    color: '#666666',
  },
  quickActions: {
    flexDirection: 'row',
    paddingHorizontal: 20,
    gap: 12,
  },
  actionButton: {
    flex: 1,
    backgroundColor: '#ffffff',
    padding: 16,
    borderRadius: 12,
    alignItems: 'center',
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 4,
    elevation: 3,
  },
  actionIcon: {
    fontSize: 24,
    marginBottom: 8,
  },
  actionText: {
    fontSize: 14,
    color: '#1a1a1a',
    fontWeight: '500',
  },
});

export default HomeScreen;
