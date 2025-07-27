import React, { useState, useEffect } from 'react';
import {
  View,
  StyleSheet,
  SafeAreaView,
  ScrollView,
  TouchableOpacity,
  Image,
  Alert,
  ActivityIndicator,
  RefreshControl,
  Switch,
} from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { useAuth } from '../../context/AuthContext';
import { useKartTheme } from '../../contexts/KartThemeContext';
import { useNavigation } from '@react-navigation/native';
import { StackNavigationProp } from '@react-navigation/stack';
import { RootStackParamList } from '../../types';

// ქართული კომპონენტები
import {
  KartTeqsti,
  KartKarti,
  KartGhilaki
} from '../../components/common';

type ProfileScreenNavigationProp = StackNavigationProp<RootStackParamList>;

const ProfileScreen: React.FC = () => {
  const navigation = useNavigation<ProfileScreenNavigationProp>();
  const { state, dispatch } = useAuth();
  const theme = useKartTheme();

  const [indebs, setIndebs] = useState(false);
  const [axlisGanaxleba, setAxlisGanaxleba] = useState(false);
  const [shetshavenebeli, setShetshavenebeli] = useState({
    shetyobinebebi: true,
    eleqtronuli_fosta: false,
    mwifi_rejimi: false,
  });

  // პროფილის მონაცემები (მოკითხვა)
  const profilis_monacemebi = {
    saxeli: state.user?.firstName || 'მხატვარი',
    gvari: state.user?.lastName || '',
    eleqtronuli_fosta: state.user?.email || 'artist@example.com',
    telefoni: state.user?.phone || '+995 555 123 456',
    profilis_surat: state.user?.profileImage || 'https://via.placeholder.com/100',
    namushevrebis_raodenoba: 42,
    gamomwer: 156,
    miyveba: 89,
    shefaseba: 4.8,
  };

  // სტატისტიკის ელემენტები
  const statistikis_elementos = [
    {
      dasaxeleba: 'ნამუშევრები',
      mnisvneloba: profilis_monacemebi.namushevrebis_raodenoba,
      xatuli: 'images-outline' as keyof typeof Ionicons.glyphMap,
      feri: theme.colors.gold[500],
    },
    {
      dasaxeleba: 'გამომწერები',
      mnisvneloba: profilis_monacemebi.gamomwer,
      xatuli: 'people-outline' as keyof typeof Ionicons.glyphMap,
      feri: theme.colors.info[500],
    },
    {
      dasaxeleba: 'მიყვება',
      mnisvneloba: profilis_monacemebi.miyveba,
      xatuli: 'heart-outline' as keyof typeof Ionicons.glyphMap,
      feri: theme.colors.error[500],
    },
    {
      dasaxeleba: 'შეფასება',
      mnisvneloba: profilis_monacemebi.shefaseba,
      xatuli: 'star-outline' as keyof typeof Ionicons.glyphMap,
      feri: theme.colors.warning[500],
    },
  ];

  // პარამეტრების მენიუს ელემენტები
  const parametrebis_menius_elementos = [
    {
      id: 'profilis_redaqtireba',
      dasaxeleba: 'პროფილის რედაქტირება',
      aghwera: 'პირადი ინფორმაციის შეცვლა',
      xatuli: 'person-outline' as keyof typeof Ionicons.glyphMap,
      moqmedeba: () => console.log('Edit profile'),
    },
    {
      id: 'chemi_namushevrebi',
      dasaxeleba: 'ჩემი ნამუშევრები',
      aghwera: 'თქვენი ატვირთული ნამუშევრების ნახვა',
      xatuli: 'images-outline' as keyof typeof Ionicons.glyphMap,
      moqmedeba: () => console.log('My artworks'),
    },
    {
      id: 'nakurnalebuli',
      dasaxeleba: 'ნაკურნალებული',
      aghwera: 'მოწონებული ნამუშევრები',
      xatuli: 'heart-outline' as keyof typeof Ionicons.glyphMap,
      moqmedeba: () => console.log('Favorites'),
    },
    {
      id: 'istoria',
      dasaxeleba: 'ისტორია',
      aghwera: 'ნანახი ნამუშევრების ისტორია',
      xatuli: 'time-outline' as keyof typeof Ionicons.glyphMap,
      moqmedeba: () => console.log('History'),
    },
    {
      id: 'parametrebi',
      dasaxeleba: 'პარამეტრები',
      aghwera: 'აპლიკაციის კონფიგურაცია',
      xatuli: 'settings-outline' as keyof typeof Ionicons.glyphMap,
      moqmedeba: () => console.log('Settings'),
    },
    {
      id: 'daxmareba',
      dasaxeleba: 'დახმარება',
      aghwera: 'ხშირად დასმული კითხვები და მხარდაჭერა',
      xatuli: 'help-circle-outline' as keyof typeof Ionicons.glyphMap,
      moqmedeba: () => console.log('Help'),
    },
  ];

  // გასვლის ფუნქცია
  const gasvla = () => {
    Alert.alert(
      'გასვლა',
      'დარწმუნებული ხართ, რომ გსურთ გასვლა?',
      [
        { text: 'გაუქმება', style: 'cancel' },
        {
          text: 'გასვლა',
          style: 'destructive',
          onPress: () => {
            dispatch({ type: 'LOGOUT' });
          },
        },
      ]
    );
  };

  // განახლების მართვა
  const ganaxlebis_martva = () => {
    setAxlisGanaxleba(true);
    // პროფილის მონაცემების განახლება
    setTimeout(() => {
      setAxlisGanaxleba(false);
    }, 1000);
  };

  return (
    <SafeAreaView style={[styles.container, { backgroundColor: theme.colors.background }]}>
      <ScrollView
        style={styles.scrollView}
        showsVerticalScrollIndicator={false}
        refreshControl={
          <RefreshControl
            refreshing={axlisGanaxleba}
            onRefresh={ganaxlebis_martva}
            tintColor={theme.colors.gold[600]}
            colors={[theme.colors.gold[600]]}
          />
        }
      >
        {/* პროფილის ზედა ნაწილი */}
        <View style={[styles.profileHeader, { paddingHorizontal: theme.spacing.lg }]}>
          <View style={styles.profileImageContainer}>
            <Image
              source={{ uri: profilis_monacemebi.profilis_surat }}
              style={styles.profileImage}
            />
            <TouchableOpacity
              style={[styles.editImageButton, { backgroundColor: theme.colors.gold[600] }]}
            >
              <Ionicons name="camera" size={16} color="white" />
            </TouchableOpacity>
          </View>

          <View style={styles.profileInfo}>
            <KartTeqsti
              shvilebi={`${profilis_monacemebi.saxeli} ${profilis_monacemebi.gvari}`}
              varianti="satauri3"
              feri={theme.colors.text}
              teqstisGaswporeba="centri"
            />
            <KartTeqsti
              shvilebi={profilis_monacemebi.eleqtronuli_fosta}
              varianti="paragraphi"
              feri={theme.colors.textSecondary}
              stili={{ marginTop: 4 }}
              teqstisGaswporeba="centri"
            />
            <KartTeqsti
              shvilebi={profilis_monacemebi.telefoni}
              varianti="patara"
              feri={theme.colors.textSecondary}
              stili={{ marginTop: 2 }}
              teqstisGaswporeba="centri"
            />
          </View>
        </View>

        {/* სტატისტიკა */}
        <View style={[styles.statsContainer, { paddingHorizontal: theme.spacing.lg }]}>
          {statistikis_elementos.map((statistika, index) => (
            <KartKarti
              key={statistika.dasaxeleba}
              varianti="standartuli"
              stili={[styles.statCard, index !== statistikis_elementos.length - 1 && { marginRight: 12 }]}
              shvilebi={
                <View style={styles.statContent}>
                  <View style={[styles.statIcon, { backgroundColor: statistika.feri + '20' }]}>
                    <Ionicons
                      name={statistika.xatuli}
                      size={20}
                      color={statistika.feri}
                    />
                  </View>
                  <KartTeqsti
                    shvilebi={statistika.mnisvneloba.toString()}
                    varianti="satauri4"
                    feri={theme.colors.text}
                    teqstisGaswporeba="centri"
                    stili={{ marginTop: 8 }}
                  />
                  <KartTeqsti
                    shvilebi={statistika.dasaxeleba}
                    varianti="dzalianPatara"
                    feri={theme.colors.textSecondary}
                    teqstisGaswporeba="centri"
                    stili={{ marginTop: 4 }}
                  />
                </View>
              }
            />
          ))}
        </View>

        {/* შეტყობინებების პარამეტრები */}
        <View style={[styles.section, { paddingHorizontal: theme.spacing.lg }]}>
          <KartTeqsti
            shvilebi="შეტყობინებები"
            varianti="satauri4"
            feri={theme.colors.gold[600]}
            stili={{ marginBottom: theme.spacing.md }}
          />

          <KartKarti
            varianti="standartuli"
            stili={styles.notificationCard}
            shvilebi={
              <View>
                <View style={styles.notificationItem}>
                  <View style={styles.notificationText}>
                    <KartTeqsti
                      shvilebi="Push შეტყობინებები"
                      varianti="paragraphi"
                      wona="sashualod"
                      feri={theme.colors.text}
                    />
                    <KartTeqsti
                      shvilebi="ახალი ნამუშევრებისა და აქტივობების შესახებ"
                      varianti="patara"
                      feri={theme.colors.textSecondary}
                    />
                  </View>
                  <Switch
                    value={shetshavenebeli.shetyobinebebi}
                    onValueChange={(mnisvneloba) =>
                      setShetshavenebeli(winapotuli => ({
                        ...winapotuli,
                        shetyobinebebi: mnisvneloba
                      }))
                    }
                    trackColor={{ false: theme.colors.border, true: theme.colors.gold[300] }}
                    thumbColor={shetshavenebeli.shetyobinebebi ? theme.colors.gold[600] : theme.colors.surface}
                  />
                </View>

                <View style={[styles.notificationItem, { borderTopColor: theme.colors.border }]}>
                  <View style={styles.notificationText}>
                    <KartTeqsti
                      shvilebi="ელექტრონული ფოსტა"
                      varianti="paragraphi"
                      wona="sashualod"
                      feri={theme.colors.text}
                    />
                    <KartTeqsti
                      shvilebi="კვირის დაჯამებები და განახლებები"
                      varianti="patara"
                      feri={theme.colors.textSecondary}
                    />
                  </View>
                  <Switch
                    value={shetshavenebeli.eleqtronuli_fosta}
                    onValueChange={(mnisvneloba) =>
                      setShetshavenebeli(winapotuli => ({
                        ...winapotuli,
                        eleqtronuli_fosta: mnisvneloba
                      }))
                    }
                    trackColor={{ false: theme.colors.border, true: theme.colors.gold[300] }}
                    thumbColor={shetshavenebeli.eleqtronuli_fosta ? theme.colors.gold[600] : theme.colors.surface}
                  />
                </View>
              </View>
            }
          />
        </View>

        {/* მენიუს ელემენტები */}
        <View style={[styles.section, { paddingHorizontal: theme.spacing.lg }]}>
          <KartTeqsti
            shvilebi="ანგარიში"
            varianti="satauri4"
            feri={theme.colors.gold[600]}
            stili={{ marginBottom: theme.spacing.md }}
          />

          {parametrebis_menius_elementos.map((elemento) => (
            <KartKarti
              key={elemento.id}
              varianti="standartuli"
              stili={styles.menuItem}
              daaklaki={elemento.moqmedeba}
              shvilebi={
                <View style={styles.menuItemContent}>
                  <View style={[styles.menuIcon, { backgroundColor: theme.colors.gold[100] }]}>
                    <Ionicons
                      name={elemento.xatuli}
                      size={20}
                      color={theme.colors.gold[600]}
                    />
                  </View>
                  <View style={styles.menuText}>
                    <KartTeqsti
                      shvilebi={elemento.dasaxeleba}
                      varianti="paragraphi"
                      wona="sashualod"
                      feri={theme.colors.text}
                    />
                    <KartTeqsti
                      shvilebi={elemento.aghwera}
                      varianti="patara"
                      feri={theme.colors.textSecondary}
                      stili={{ marginTop: 2 }}
                    />
                  </View>
                  <Ionicons
                    name="chevron-forward"
                    size={20}
                    color={theme.colors.textSecondary}
                  />
                </View>
              }
            />
          ))}
        </View>

        {/* გასვლის ღილაკი */}
        <View style={[styles.logoutContainer, { paddingHorizontal: theme.spacing.lg }]}>
          <KartGhilaki
            satauri="გასვლა"
            varianti="safrtkhe"
            zoma="sashualod"
            stili={styles.logoutButton}
            daaklaki={gasvla}
          />
        </View>

        {/* ქვედა ინფორმაცია */}
        <View style={[styles.footer, { paddingHorizontal: theme.spacing.lg }]}>
          <KartTeqsti
            shvilebi="ACUMEN CRAFT v1.0.0"
            varianti="dzalianPatara"
            feri={theme.colors.textSecondary}
            teqstisGaswporeba="centri"
          />
          <KartTeqsti
            shvilebi="© 2024 ქართული ხელოვნების პლატფორმა"
            varianti="dzalianPatara"
            feri={theme.colors.textSecondary}
            teqstisGaswporeba="centri"
            stili={{ marginTop: 4 }}
          />
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
  scrollView: {
    flex: 1,
  },
  profileHeader: {
    alignItems: 'center',
    paddingVertical: 32,
  },
  profileImageContainer: {
    position: 'relative',
    marginBottom: 16,
  },
  profileImage: {
    width: 100,
    height: 100,
    borderRadius: 50,
  },
  editImageButton: {
    position: 'absolute',
    bottom: 0,
    right: 0,
    width: 32,
    height: 32,
    borderRadius: 16,
    justifyContent: 'center',
    alignItems: 'center',
    borderWidth: 3,
    borderColor: 'white',
  },
  profileInfo: {
    alignItems: 'center',
  },
  statsContainer: {
    flexDirection: 'row',
    marginBottom: 32,
  },
  statCard: {
    flex: 1,
    paddingVertical: 20,
  },
  statContent: {
    alignItems: 'center',
  },
  statIcon: {
    width: 40,
    height: 40,
    borderRadius: 20,
    justifyContent: 'center',
    alignItems: 'center',
  },
  section: {
    marginBottom: 32,
  },
  notificationCard: {
    padding: 0,
    overflow: 'hidden',
  },
  notificationItem: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    paddingHorizontal: 16,
    paddingVertical: 16,
    borderTopWidth: 1,
    borderTopColor: 'transparent',
  },
  notificationText: {
    flex: 1,
    marginRight: 16,
  },
  menuItem: {
    marginBottom: 12,
    padding: 0,
    overflow: 'hidden',
  },
  menuItemContent: {
    flexDirection: 'row',
    alignItems: 'center',
    padding: 16,
  },
  menuIcon: {
    width: 40,
    height: 40,
    borderRadius: 20,
    justifyContent: 'center',
    alignItems: 'center',
    marginRight: 16,
  },
  menuText: {
    flex: 1,
  },
  logoutContainer: {
    marginBottom: 32,
  },
  logoutButton: {
    width: '100%',
  },
  footer: {
    paddingVertical: 24,
    alignItems: 'center',
  },
});

export default ProfileScreen;
