import React, { useState, useEffect } from 'react';
import {
  View,
  Text,
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
import { Artwork, RootStackParamList } from '../../types';

type ExploreScreenNavigationProp = StackNavigationProp<RootStackParamList>;

const { width } = Dimensions.get('window');
const ITEM_WIDTH = (width - 60) / 2; // 2 columns with margins

const ExploreScreen: React.FC = () => {
  const navigation = useNavigation<ExploreScreenNavigationProp>();
  const { state } = useAuth();
  const [artworks, setArtworks] = useState<Artwork[]>([]);
  const [filteredArtworks, setFilteredArtworks] = useState<Artwork[]>([]);
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);
  const [searchQuery, setSearchQuery] = useState('');
  const [selectedCategory, setSelectedCategory] = useState<string>('all');
  const [page, setPage] = useState(1);
  const [hasMore, setHasMore] = useState(true);
  const [loadingMore, setLoadingMore] = useState(false);

  const artworkService = new ArtworkService();

  // Categories for filtering
  const categories = [
    { id: 'all', name: 'All', icon: '🎨' },
    { id: 'image', name: 'Images', icon: '🖼️' },
    { id: 'video', name: 'Videos', icon: '🎬' },
    { id: 'audio', name: 'Audio', icon: '🎵' },
    { id: 'ai', name: 'AI Generated', icon: '🤖' },
  ];

  // Load artworks
  const loadArtworks = async (pageNum: number = 1, isRefresh: boolean = false) => {
    try {
      if (isRefresh) {
        setPage(1);
        setHasMore(true);
      }

      const response = await artworkService.getArtworks(pageNum, 20);

      if (isRefresh || pageNum === 1) {
        setArtworks(response.data);
      } else {
        setArtworks(prev => [...prev, ...response.data]);
      }

      setHasMore(pageNum < response.last_page);

    } catch (error) {
      console.error('Failed to load artworks:', error);
      Alert.alert('Error', 'Failed to load artworks');
    } finally {
      setLoading(false);
      setRefreshing(false);
      setLoadingMore(false);
    }
  };

  // Initial load
  useEffect(() => {
    loadArtworks();
  }, []);

  // Filter artworks based on search and category
  useEffect(() => {
    let filtered = [...artworks];

    // Apply search filter
    if (searchQuery.trim()) {
      filtered = filtered.filter(artwork => {
        const title = getArtworkTitle(artwork).toLowerCase();
        const tags = artwork.tags?.toLowerCase() || '';
        const artist = artwork.user?.name?.toLowerCase() || '';
        const query = searchQuery.toLowerCase();

        return title.includes(query) || tags.includes(query) || artist.includes(query);
      });
    }

    // Apply category filter
    if (selectedCategory !== 'all') {
      if (selectedCategory === 'ai') {
        filtered = filtered.filter(artwork => artwork.is_ai_generated);
      } else {
        filtered = filtered.filter(artwork => artwork.media_type === selectedCategory);
      }
    }

    setFilteredArtworks(filtered);
  }, [artworks, searchQuery, selectedCategory]);

  // Get artwork title
  const getArtworkTitle = (artwork: Artwork): string => {
    const titles = artwork.title;
    if (typeof titles === 'string') return titles;

    const userLang = state.user?.lang || 'en';
    return titles[userLang] || titles.en || titles.ka || titles.de || 'Untitled';
  };

  // Handle refresh
  const onRefresh = () => {
    setRefreshing(true);
    loadArtworks(1, true);
  };

  // Handle load more
  const onLoadMore = () => {
    if (!loadingMore && hasMore) {
      setLoadingMore(true);
      const nextPage = page + 1;
      setPage(nextPage);
      loadArtworks(nextPage);
    }
  };

  // Handle artwork press
  const handleArtworkPress = (artwork: Artwork) => {
    navigation.navigate('ArtworkDetail', { artworkId: artwork.id });
  };

  // Render artwork item
  const renderArtworkItem = ({ item }: { item: Artwork }) => (
    <TouchableOpacity
      style={styles.artworkItem}
      onPress={() => handleArtworkPress(item)}
    >
      <Image
        source={{
          uri: item.media_url || `https://picsum.photos/300/200?random=${item.id}`
        }}
        style={styles.artworkImage}
      />

      {/* AI Generated Badge */}
      {item.is_ai_generated && (
        <View style={styles.aiBadge}>
          <Text style={styles.aiBadgeText}>🤖 AI</Text>
        </View>
      )}

      {/* ACQ Score Badge */}
      {item.acq_score && (
        <View style={styles.acqBadge}>
          <Text style={styles.acqBadgeText}>ACQ {item.acq_score.toFixed(1)}</Text>
        </View>
      )}

      <View style={styles.artworkInfo}>
        <Text style={styles.artworkTitle} numberOfLines={2}>
          {getArtworkTitle(item)}
        </Text>
        <Text style={styles.artworkArtist} numberOfLines={1}>
          by {item.user?.name || 'Unknown Artist'}
        </Text>

        <View style={styles.artworkStats}>
          <View style={styles.statItem}>
            <Ionicons name="heart" size={14} color="#ff6b6b" />
            <Text style={styles.statText}>{item.likes_count || 0}</Text>
          </View>
          <View style={styles.statItem}>
            <Ionicons name="chatbubble" size={14} color="#4dabf7" />
            <Text style={styles.statText}>{item.comments_count || 0}</Text>
          </View>
          <View style={styles.statItem}>
            <Ionicons name="eye" size={14} color="#51cf66" />
            <Text style={styles.statText}>{item.views_count || 0}</Text>
          </View>
        </View>
      </View>
    </TouchableOpacity>
  );

  if (loading) {
    return (
      <SafeAreaView style={styles.container}>
        <View style={styles.loadingContainer}>
          <ActivityIndicator size="large" color="#007AFF" />
          <Text style={styles.loadingText}>Loading artworks...</Text>
        </View>
      </SafeAreaView>
    );
  }

  return (
    <SafeAreaView style={styles.container}>
      {/* Header */}
      <View style={styles.header}>
        <Text style={styles.title}>Explore</Text>
        <TouchableOpacity style={styles.searchButton}>
          <Ionicons name="search" size={24} color="#007AFF" />
        </TouchableOpacity>
      </View>

      {/* Search Bar */}
      <View style={styles.searchContainer}>
        <View style={styles.searchBar}>
          <Ionicons name="search" size={20} color="#666666" />
          <TextInput
            style={styles.searchInput}
            placeholder="Search artworks, artists, tags..."
            value={searchQuery}
            onChangeText={setSearchQuery}
            autoCapitalize="none"
            autoCorrect={false}
          />
          {searchQuery.length > 0 && (
            <TouchableOpacity onPress={() => setSearchQuery('')}>
              <Ionicons name="close-circle" size={20} color="#666666" />
            </TouchableOpacity>
          )}
        </View>
      </View>

      {/* Categories */}
      <ScrollView
        horizontal
        showsHorizontalScrollIndicator={false}
        style={styles.categoriesContainer}
        contentContainerStyle={styles.categoriesContent}
      >
        {categories.map((category) => (
          <TouchableOpacity
            key={category.id}
            style={[
              styles.categoryButton,
              selectedCategory === category.id && styles.categoryButtonActive
            ]}
            onPress={() => setSelectedCategory(category.id)}
          >
            <Text style={styles.categoryIcon}>{category.icon}</Text>
            <Text style={[
              styles.categoryText,
              selectedCategory === category.id && styles.categoryTextActive
            ]}>
              {category.name}
            </Text>
          </TouchableOpacity>
        ))}
      </ScrollView>

      {/* Results Count */}
      <View style={styles.resultsContainer}>
        <Text style={styles.resultsText}>
          {filteredArtworks.length} artwork{filteredArtworks.length !== 1 ? 's' : ''} found
        </Text>
      </View>

      {/* Artworks Grid */}
      <FlatList
        data={filteredArtworks}
        renderItem={renderArtworkItem}
        keyExtractor={(item) => item.id.toString()}
        numColumns={2}
        contentContainerStyle={styles.artworksContainer}
        refreshControl={
          <RefreshControl refreshing={refreshing} onRefresh={onRefresh} />
        }
        onEndReached={onLoadMore}
        onEndReachedThreshold={0.1}
        ListFooterComponent={
          loadingMore ? (
            <View style={styles.loadMoreContainer}>
              <ActivityIndicator size="small" color="#007AFF" />
            </View>
          ) : null
        }
        ListEmptyComponent={
          <View style={styles.emptyContainer}>
            <Text style={styles.emptyIcon}>🎨</Text>
            <Text style={styles.emptyTitle}>No artworks found</Text>
            <Text style={styles.emptySubtitle}>
              {searchQuery.trim()
                ? 'Try adjusting your search terms'
                : 'Be the first to share your creativity!'}
            </Text>
          </View>
        }
      />
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
  header: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    paddingHorizontal: 20,
    paddingVertical: 16,
    backgroundColor: '#ffffff',
    borderBottomWidth: 1,
    borderBottomColor: '#e1e1e1',
  },
  title: {
    fontSize: 28,
    fontWeight: 'bold',
    color: '#1a1a1a',
  },
  searchButton: {
    padding: 8,
  },
  searchContainer: {
    paddingHorizontal: 20,
    paddingVertical: 16,
    backgroundColor: '#ffffff',
  },
  searchBar: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: '#f5f5f5',
    borderRadius: 12,
    paddingHorizontal: 16,
    paddingVertical: 12,
    gap: 12,
  },
  searchInput: {
    flex: 1,
    fontSize: 16,
    color: '#1a1a1a',
  },
  categoriesContainer: {
    backgroundColor: '#ffffff',
    borderBottomWidth: 1,
    borderBottomColor: '#e1e1e1',
  },
  categoriesContent: {
    paddingHorizontal: 20,
    paddingVertical: 16,
    gap: 12,
  },
  categoryButton: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingHorizontal: 16,
    paddingVertical: 8,
    backgroundColor: '#f5f5f5',
    borderRadius: 20,
    gap: 8,
  },
  categoryButtonActive: {
    backgroundColor: '#007AFF',
  },
  categoryIcon: {
    fontSize: 16,
  },
  categoryText: {
    fontSize: 14,
    fontWeight: '500',
    color: '#666666',
  },
  categoryTextActive: {
    color: '#ffffff',
  },
  resultsContainer: {
    paddingHorizontal: 20,
    paddingVertical: 12,
    backgroundColor: '#ffffff',
  },
  resultsText: {
    fontSize: 14,
    color: '#666666',
  },
  artworksContainer: {
    padding: 20,
    gap: 16,
  },
  artworkItem: {
    width: ITEM_WIDTH,
    marginHorizontal: 8,
    marginBottom: 16,
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
    backgroundColor: '#f5f5f5',
  },
  aiBadge: {
    position: 'absolute',
    top: 8,
    left: 8,
    backgroundColor: 'rgba(138, 43, 226, 0.9)',
    paddingHorizontal: 6,
    paddingVertical: 2,
    borderRadius: 8,
  },
  aiBadgeText: {
    color: '#ffffff',
    fontSize: 10,
    fontWeight: '600',
  },
  acqBadge: {
    position: 'absolute',
    top: 8,
    right: 8,
    backgroundColor: 'rgba(0, 122, 255, 0.9)',
    paddingHorizontal: 6,
    paddingVertical: 2,
    borderRadius: 8,
  },
  acqBadgeText: {
    color: '#ffffff',
    fontSize: 10,
    fontWeight: '600',
  },
  artworkInfo: {
    padding: 12,
  },
  artworkTitle: {
    fontSize: 14,
    fontWeight: '600',
    color: '#1a1a1a',
    marginBottom: 4,
  },
  artworkArtist: {
    fontSize: 12,
    color: '#666666',
    marginBottom: 8,
  },
  artworkStats: {
    flexDirection: 'row',
    justifyContent: 'space-between',
  },
  statItem: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 4,
  },
  statText: {
    fontSize: 12,
    color: '#666666',
  },
  loadMoreContainer: {
    paddingVertical: 20,
    alignItems: 'center',
  },
  emptyContainer: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
    paddingTop: 60,
  },
  emptyIcon: {
    fontSize: 48,
    marginBottom: 16,
  },
  emptyTitle: {
    fontSize: 18,
    fontWeight: '600',
    color: '#1a1a1a',
    marginBottom: 8,
  },
  emptySubtitle: {
    fontSize: 14,
    color: '#666666',
    textAlign: 'center',
    paddingHorizontal: 40,
  },
});

export default ExploreScreen;
