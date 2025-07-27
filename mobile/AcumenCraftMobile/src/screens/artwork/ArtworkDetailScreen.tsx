import React, { useState, useEffect } from 'react';
import {
  View,
  Text,
  StyleSheet,
  SafeAreaView,
  ScrollView,
  Image,
  TouchableOpacity,
  ActivityIndicator,
  Alert,
  Dimensions,
  Share,
} from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { useRoute, useNavigation } from '@react-navigation/native';
import { RouteProp } from '@react-navigation/native';
import { StackNavigationProp } from '@react-navigation/stack';
import { ArtworkService } from '../../services/api/artworkService';
import { useAuth } from '../../context/AuthContext';
import { Artwork, Comment, RootStackParamList } from '../../types';

type ArtworkDetailRouteProp = RouteProp<RootStackParamList, 'ArtworkDetail'>;
type ArtworkDetailNavigationProp = StackNavigationProp<RootStackParamList, 'ArtworkDetail'>;

const { width } = Dimensions.get('window');

const ArtworkDetailScreen: React.FC = () => {
  const route = useRoute<ArtworkDetailRouteProp>();
  const navigation = useNavigation<ArtworkDetailNavigationProp>();
  const { state } = useAuth();
  const { artworkId } = route.params;

  const [artwork, setArtwork] = useState<Artwork | null>(null);
  const [comments, setComments] = useState<Comment[]>([]);
  const [loading, setLoading] = useState(true);
  const [isLiked, setIsLiked] = useState(false);
  const [likesCount, setLikesCount] = useState(0);

  const artworkService = new ArtworkService();

  // Load artwork details
  const loadArtworkDetail = async () => {
    try {
      const artworkData = await artworkService.getArtwork(artworkId);
      setArtwork(artworkData);
      setLikesCount(artworkData.likes_count || 0);

      // Load comments
      const commentsData = await artworkService.getArtworkComments(artworkId);
      setComments(commentsData);

    } catch (error) {
      console.error('Failed to load artwork:', error);
      Alert.alert('Error', 'Failed to load artwork details');
      navigation.goBack();
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    loadArtworkDetail();
  }, [artworkId]);

  // Get artwork title
  const getArtworkTitle = (artwork: Artwork): string => {
    const titles = artwork.title;
    if (typeof titles === 'string') return titles;

    const userLang = state.user?.lang || 'en';
    return titles[userLang] || titles.en || titles.ka || titles.de || 'Untitled';
  };

  // Get artwork description
  const getArtworkDescription = (artwork: Artwork): string => {
    const descriptions = artwork.description;
    if (typeof descriptions === 'string') return descriptions;

    const userLang = state.user?.lang || 'en';
    return descriptions[userLang] || descriptions.en || descriptions.ka || descriptions.de || '';
  };

  // Handle like toggle
  const handleLikeToggle = async () => {
    try {
      const result = await artworkService.toggleLike(artworkId);
      setIsLiked(result.liked);
      setLikesCount(result.likes_count);
    } catch (error) {
      console.error('Failed to toggle like:', error);
      Alert.alert('Error', 'Failed to update like status');
    }
  };

  // Handle share
  const handleShare = async () => {
    if (!artwork) return;

    try {
      await Share.share({
        message: `Check out "${getArtworkTitle(artwork)}" by ${artwork.user?.name || 'Unknown Artist'} on Acumen Craft!`,
        url: artwork.media_url, // In a real app, this would be a deep link
      });
    } catch (error) {
      console.error('Failed to share:', error);
    }
  };

  // Format date
  const formatDate = (dateString: string): string => {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
      year: 'numeric',
      month: 'short',
      day: 'numeric',
    });
  };

  if (loading) {
    return (
      <SafeAreaView style={styles.container}>
        <View style={styles.loadingContainer}>
          <ActivityIndicator size="large" color="#007AFF" />
          <Text style={styles.loadingText}>Loading artwork...</Text>
        </View>
      </SafeAreaView>
    );
  }

  if (!artwork) {
    return (
      <SafeAreaView style={styles.container}>
        <View style={styles.errorContainer}>
          <Text style={styles.errorText}>Artwork not found</Text>
          <TouchableOpacity style={styles.backButton} onPress={() => navigation.goBack()}>
            <Text style={styles.backButtonText}>Go Back</Text>
          </TouchableOpacity>
        </View>
      </SafeAreaView>
    );
  }

  return (
    <SafeAreaView style={styles.container}>
      <ScrollView style={styles.scrollView}>
        {/* Artwork Image */}
        <View style={styles.imageContainer}>
          <Image
            source={{
              uri: artwork.media_url || `https://picsum.photos/400/300?random=${artwork.id}`
            }}
            style={styles.artworkImage}
            resizeMode="cover"
          />

          {/* Overlay badges */}
          <View style={styles.badgeContainer}>
            {artwork.is_ai_generated && (
              <View style={styles.aiBadge}>
                <Text style={styles.badgeText}>🤖 AI Generated</Text>
              </View>
            )}
            {artwork.acq_score && (
              <View style={styles.acqBadge}>
                <Text style={styles.badgeText}>ACQ {artwork.acq_score.toFixed(1)}</Text>
              </View>
            )}
          </View>
        </View>

        {/* Artwork Info */}
        <View style={styles.contentContainer}>
          {/* Title and Artist */}
          <View style={styles.headerSection}>
            <Text style={styles.artworkTitle}>{getArtworkTitle(artwork)}</Text>
            <TouchableOpacity style={styles.artistContainer}>
              <Text style={styles.artistName}>by {artwork.user?.name || 'Unknown Artist'}</Text>
              <Text style={styles.publishDate}>Published {formatDate(artwork.created_at)}</Text>
            </TouchableOpacity>
          </View>

          {/* Action Buttons */}
          <View style={styles.actionContainer}>
            <TouchableOpacity
              style={[styles.actionButton, isLiked && styles.actionButtonLiked]}
              onPress={handleLikeToggle}
            >
              <Ionicons
                name={isLiked ? "heart" : "heart-outline"}
                size={20}
                color={isLiked ? "#ffffff" : "#007AFF"}
              />
              <Text style={[styles.actionText, isLiked && styles.actionTextLiked]}>
                {likesCount}
              </Text>
            </TouchableOpacity>

            <TouchableOpacity style={styles.actionButton}>
              <Ionicons name="chatbubble-outline" size={20} color="#007AFF" />
              <Text style={styles.actionText}>{comments.length}</Text>
            </TouchableOpacity>

            <TouchableOpacity style={styles.actionButton} onPress={handleShare}>
              <Ionicons name="share-outline" size={20} color="#007AFF" />
              <Text style={styles.actionText}>Share</Text>
            </TouchableOpacity>

            <TouchableOpacity style={styles.actionButton}>
              <Ionicons name="star-outline" size={20} color="#007AFF" />
              <Text style={styles.actionText}>Evaluate</Text>
            </TouchableOpacity>
          </View>

          {/* Description */}
          {getArtworkDescription(artwork) && (
            <View style={styles.descriptionSection}>
              <Text style={styles.sectionTitle}>Description</Text>
              <Text style={styles.description}>{getArtworkDescription(artwork)}</Text>
            </View>
          )}

          {/* Tags */}
          {artwork.tags && (
            <View style={styles.tagsSection}>
              <Text style={styles.sectionTitle}>Tags</Text>
              <View style={styles.tagsContainer}>
                {artwork.tags.split(',').map((tag, index) => (
                  <View key={index} style={styles.tag}>
                    <Text style={styles.tagText}>#{tag.trim()}</Text>
                  </View>
                ))}
              </View>
            </View>
          )}

          {/* Technical Info */}
          <View style={styles.technicalSection}>
            <Text style={styles.sectionTitle}>Details</Text>
            <View style={styles.technicalGrid}>
              <View style={styles.technicalItem}>
                <Text style={styles.technicalLabel}>Type</Text>
                <Text style={styles.technicalValue}>{artwork.media_type}</Text>
              </View>
              <View style={styles.technicalItem}>
                <Text style={styles.technicalLabel}>License</Text>
                <Text style={styles.technicalValue}>{artwork.license}</Text>
              </View>
              <View style={styles.technicalItem}>
                <Text style={styles.technicalLabel}>Views</Text>
                <Text style={styles.technicalValue}>{artwork.views_count || 0}</Text>
              </View>
              <View style={styles.technicalItem}>
                <Text style={styles.technicalLabel}>Visibility</Text>
                <Text style={styles.technicalValue}>{artwork.visibility}</Text>
              </View>
            </View>
          </View>

          {/* Comments Section */}
          <View style={styles.commentsSection}>
            <View style={styles.commentHeader}>
              <Text style={styles.sectionTitle}>Comments ({comments.length})</Text>
              <TouchableOpacity style={styles.addCommentButton}>
                <Ionicons name="add" size={20} color="#007AFF" />
                <Text style={styles.addCommentText}>Add Comment</Text>
              </TouchableOpacity>
            </View>

            {comments.length > 0 ? (
              comments.map((comment) => (
                <View key={comment.id} style={styles.commentItem}>
                  <View style={styles.commentHeader}>
                    <Text style={styles.commentAuthor}>{comment.user?.name || 'Anonymous'}</Text>
                    <Text style={styles.commentDate}>{formatDate(comment.created_at)}</Text>
                  </View>
                  <Text style={styles.commentContent}>{comment.content}</Text>
                </View>
              ))
            ) : (
              <View style={styles.noCommentsContainer}>
                <Text style={styles.noCommentsText}>No comments yet</Text>
                <Text style={styles.noCommentsSubtext}>Be the first to share your thoughts!</Text>
              </View>
            )}
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
  scrollView: {
    flex: 1,
  },
  loadingContainer: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
    padding: 20,
  },
  loadingText: {
    marginTop: 10,
    fontSize: 16,
    color: '#666',
  },
  errorContainer: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
    padding: 20,
  },
  errorText: {
    fontSize: 18,
    color: '#e74c3c',
    marginBottom: 20,
    textAlign: 'center',
  },
  backButton: {
    backgroundColor: '#007AFF',
    paddingHorizontal: 20,
    paddingVertical: 10,
    borderRadius: 8,
  },
  backButtonText: {
    color: '#ffffff',
    fontSize: 16,
    fontWeight: '600',
  },
  imageContainer: {
    position: 'relative',
    width: width,
    height: width * 0.75,
    backgroundColor: '#000',
  },
  artworkImage: {
    width: '100%',
    height: '100%',
  },
  badgeContainer: {
    position: 'absolute',
    top: 16,
    right: 16,
    flexDirection: 'column',
    gap: 8,
  },
  aiBadge: {
    backgroundColor: 'rgba(138, 43, 226, 0.9)',
    paddingHorizontal: 12,
    paddingVertical: 6,
    borderRadius: 20,
  },
  acqBadge: {
    backgroundColor: 'rgba(255, 215, 0, 0.9)',
    paddingHorizontal: 12,
    paddingVertical: 6,
    borderRadius: 20,
  },
  badgeText: {
    color: '#ffffff',
    fontSize: 12,
    fontWeight: '600',
  },
  contentContainer: {
    backgroundColor: '#ffffff',
    borderTopLeftRadius: 20,
    borderTopRightRadius: 20,
    marginTop: -20,
    paddingTop: 20,
    paddingHorizontal: 20,
    paddingBottom: 40,
  },
  headerSection: {
    marginBottom: 20,
  },
  artworkTitle: {
    fontSize: 24,
    fontWeight: 'bold',
    color: '#2c3e50',
    marginBottom: 8,
  },
  artistContainer: {
    flexDirection: 'column',
    gap: 2,
  },
  artistName: {
    fontSize: 16,
    color: '#007AFF',
    fontWeight: '600',
  },
  publishDate: {
    fontSize: 14,
    color: '#666',
  },
  actionContainer: {
    flexDirection: 'row',
    justifyContent: 'space-around',
    backgroundColor: '#f8f9fa',
    borderRadius: 12,
    paddingVertical: 16,
    marginBottom: 24,
  },
  actionButton: {
    flexDirection: 'column',
    alignItems: 'center',
    gap: 4,
    paddingHorizontal: 16,
    paddingVertical: 8,
    borderRadius: 8,
    minWidth: 60,
  },
  actionButtonLiked: {
    backgroundColor: '#e74c3c',
  },
  actionText: {
    fontSize: 12,
    color: '#007AFF',
    fontWeight: '600',
  },
  actionTextLiked: {
    color: '#ffffff',
  },
  descriptionSection: {
    marginBottom: 24,
  },
  sectionTitle: {
    fontSize: 18,
    fontWeight: 'bold',
    color: '#2c3e50',
    marginBottom: 12,
  },
  description: {
    fontSize: 16,
    color: '#555',
    lineHeight: 24,
  },
  tagsSection: {
    marginBottom: 24,
  },
  tagsContainer: {
    flexDirection: 'row',
    flexWrap: 'wrap',
    gap: 8,
  },
  tag: {
    backgroundColor: '#e3f2fd',
    paddingHorizontal: 12,
    paddingVertical: 6,
    borderRadius: 16,
  },
  tagText: {
    color: '#1976d2',
    fontSize: 14,
    fontWeight: '500',
  },
  technicalSection: {
    marginBottom: 24,
  },
  technicalGrid: {
    flexDirection: 'row',
    flexWrap: 'wrap',
    gap: 16,
  },
  technicalItem: {
    flex: 1,
    minWidth: '45%',
    backgroundColor: '#f8f9fa',
    padding: 12,
    borderRadius: 8,
  },
  technicalLabel: {
    fontSize: 12,
    color: '#666',
    fontWeight: '500',
    marginBottom: 4,
  },
  technicalValue: {
    fontSize: 14,
    color: '#2c3e50',
    fontWeight: '600',
  },
  commentsSection: {
    marginTop: 8,
  },
  commentHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: 16,
  },
  addCommentButton: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 4,
    paddingHorizontal: 12,
    paddingVertical: 6,
    backgroundColor: '#e3f2fd',
    borderRadius: 8,
  },
  addCommentText: {
    color: '#007AFF',
    fontSize: 14,
    fontWeight: '600',
  },
  commentItem: {
    backgroundColor: '#f8f9fa',
    padding: 16,
    borderRadius: 12,
    marginBottom: 12,
  },
  commentAuthor: {
    fontSize: 14,
    fontWeight: '600',
    color: '#2c3e50',
  },
  commentDate: {
    fontSize: 12,
    color: '#666',
  },
  commentContent: {
    fontSize: 14,
    color: '#555',
    lineHeight: 20,
    marginTop: 8,
  },
  noCommentsContainer: {
    alignItems: 'center',
    paddingVertical: 40,
  },
  noCommentsText: {
    fontSize: 16,
    color: '#666',
    fontWeight: '500',
    marginBottom: 4,
  },
  noCommentsSubtext: {
    fontSize: 14,
    color: '#999',
  },
});

export default ArtworkDetailScreen;
