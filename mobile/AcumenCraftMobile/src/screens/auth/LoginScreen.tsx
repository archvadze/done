import React, { useState } from 'react';
import {
  View,
  StyleSheet,
  SafeAreaView,
  KeyboardAvoidingView,
  Platform,
  ScrollView,
  Alert,
} from 'react-native';
import { useNavigation } from '@react-navigation/native';
import { StackNavigationProp } from '@react-navigation/stack';
import { useAuth } from '../../context/AuthContext';
import { useKartTheme } from '../../contexts/KartThemeContext';
import { RootStackParamList } from '../../types';

// ქართული კომპონენტები
import {
  KartTeqsti,
  KartSheyvana,
  KartGhilaki,
  KartKarti
} from '../../components/common';

type LoginScreenNavigationProp = StackNavigationProp<RootStackParamList, 'Login'>;

interface ShmosalisForma {
  emaili: string;                      // ემეილი
  paroli: string;                      // პაროლი
}

const LoginScreen: React.FC = () => {
  const navigation = useNavigation<LoginScreenNavigationProp>();
  const { login, state, clearError } = useAuth();
  const theme = useKartTheme();

  const [forma, setForma] = useState<ShmosalisForma>({
    emaili: '',
    paroli: '',
  });
  const [shecdomebi, setShecdomebi] = useState<Partial<ShmosalisForma>>({});

  // Auth error-ის გასუფთავება ფორმის ცვლილებისას
  React.useEffect(() => {
    if (state.error) {
      clearError();
    }
  }, [forma]);

  const formisShetvasSesitsema = (): boolean => {
    const axaliShecdomebi: Partial<ShmosalisForma> = {};

    if (!forma.emaili) {
      axaliShecdomebi.emaili = 'ემეილი სავალდებულოა';
    } else if (!/\S+@\S+\.\S+/.test(forma.emaili)) {
      axaliShecdomebi.emaili = 'ემეილის ფორმატი არასწორია';
    }

    if (!forma.paroli) {
      axaliShecdomebi.paroli = 'პაროლი სავალდებულოა';
    } else if (forma.paroli.length < 6) {
      axaliShecdomebi.paroli = 'პაროლი უნდა იყოს მინიმუმ 6 სიმბოლო';
    }

    setShecdomebi(axaliShecdomebi);
    return Object.keys(axaliShecdomebi).length === 0;
  };

  const shemoslisGamortvai = async () => {
    if (!formisShetvasSesitsema()) {
      return;
    }

    try {
      await login({
        email: forma.emaili,
        password: forma.paroli,
      });

      // Navigation will be handled automatically by AuthContext
      Alert.alert('წარმატება', 'შემოსვლა წარმატებული!');

    } catch (error) {
      Alert.alert(
        'შემოსვლა ვერ მოხერხდა',
        error instanceof Error ? error.message : 'გთხოვთ შეამოწმოთ მონაცემები და სცადოთ ხელახლა.'
      );
    }
  };

  const formisgganaxleba = (veli: keyof ShmosalisForma, mnishshvneloba: string) => {
    setForma(prev => ({ ...prev, [veli]: mnishshvneloba }));
    if (shecdomebi[veli]) {
      setShecdomebi(prev => ({ ...prev, [veli]: undefined }));
    }
  };

  return (
    <SafeAreaView style={[styles.container, { backgroundColor: theme.colors.background }]}>
      <KeyboardAvoidingView
        behavior={Platform.OS === 'ios' ? 'padding' : 'height'}
        style={styles.keyboardAvoidingView}
      >
        <ScrollView
          contentContainerStyle={styles.scrollContent}
          keyboardShouldPersistTaps="handled"
        >
          {/* ლოგოს სივრცე */}
          <View style={styles.logoContainer}>
            <KartTeqsti
              shvilebi="ACUMEN CRAFT"
              varianti="satauri1"
              teqstisGaswporeba="centri"
              stili={{ marginBottom: theme.spacing.sm }}
            />
            <KartTeqsti
              shvilebi="სადაც ხელოვნება შეხვდება ინტელექტს"
              varianti="paragraphi"
              feri={theme.colors.gold[400]}
              teqstisGaswporeba="centri"
            />
          </View>

          {/* შემოსვლის ფორმა */}
          <KartKarti
            varianti="standartuli"
            stili={styles.formCard}
            shvilebi={
              <>
                <KartTeqsti
                  shvilebi="შემოსვლა"
                  varianti="satauri3"
                  teqstisGaswporeba="centri"
                  stili={{ marginBottom: theme.spacing.lg }}
                />

                {/* ემეილის ველი */}
                <KartSheyvana
                  satauri="ემეილის მისამართი"
                  value={forma.emaili}
                  onChangeText={(text) => formisgganaxleba('emaili', text)}
                  placeholder="მაგ: user@example.com"
                  keyboardType="email-address"
                  autoCapitalize="none"
                  autoComplete="email"
                  surathuli="mail-outline"
                  shecsavlia={shecdomebi.emaili}
                />

                {/* პაროლის ველი */}
                <KartSheyvana
                  satauri="პაროლი"
                  value={forma.paroli}
                  onChangeText={(text) => formisgganaxleba('paroli', text)}
                  placeholder="მინიმუმ 6 სიმბოლო"
                  parolisMnishvneloba={true}
                  autoComplete="password"
                  shecsavlia={shecdomebi.paroli}
                />

                {/* შემოსვლის ღილაკი */}
                <KartGhilaki
                  satauri="შემოსვლა"
                  varianti="dziritadi"
                  zoma="didi"
                  sigrze="100%"
                  mtavaria={state.isLoading}
                  gamotvlili={state.isLoading}
                  daaklaki={shemoslisGamortvai}
                  stili={{ marginTop: theme.spacing.lg }}
                />

                {/* ან განყოფილება */}
                <View style={styles.dividerContainer}>
                  <View style={[styles.divider, { backgroundColor: theme.colors.gray[600] }]} />
                  <KartTeqsti
                    shvilebi="ან"
                    varianti="patara"
                    feri={theme.colors.gray[400]}
                    stili={styles.dividerText}
                  />
                  <View style={[styles.divider, { backgroundColor: theme.colors.gray[600] }]} />
                </View>

                {/* რეგისტრაციის ღილაკი */}
                <KartGhilaki
                  satauri="ანგარიშის შექმნა"
                  varianti="meoardi"
                  zoma="didi"
                  sigrze="100%"
                  daaklaki={() => navigation.navigate('Register')}
                />
              </>
            }
          />

          {/* Error შეტყობინება */}
          {state.error && (
            <KartKarti
              varianti="standartuli"
              stili={[styles.errorCard, { borderColor: theme.colors.error[500] }] as any}
              shvilebi={
                <KartTeqsti
                  shvilebi={state.error}
                  varianti="patara"
                  feri={theme.colors.error[500]}
                  teqstisGaswporeba="centri"
                />
              }
            />
          )}
        </ScrollView>
      </KeyboardAvoidingView>
    </SafeAreaView>
  );
};

// ქართული სტილები
const styles = StyleSheet.create({
  container: {
    flex: 1,
  },
  keyboardAvoidingView: {
    flex: 1,
  },
  scrollContent: {
    flexGrow: 1,
    paddingHorizontal: 24,
    paddingVertical: 32,
    justifyContent: 'center',
  },
  logoContainer: {
    alignItems: 'center',
    marginBottom: 48,
  },
  formCard: {
    marginHorizontal: 0,
    marginBottom: 24,
  },
  dividerContainer: {
    flexDirection: 'row',
    alignItems: 'center',
    marginVertical: 24,
  },
  divider: {
    flex: 1,
    height: 1,
  },
  dividerText: {
    marginHorizontal: 16,
  },
  errorCard: {
    marginHorizontal: 0,
    marginTop: 16,
    borderWidth: 1,
  },
});

export default LoginScreen;
