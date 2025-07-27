import React from 'react';
import { View, ViewStyle, TouchableOpacity } from 'react-native';
import { useKartTheme } from '../../contexts/KartThemeContext';

interface KartKartiProps {
  shvilebi: React.ReactNode;                 // შვილები
  varianti?: 'naxati' | 'profili' | 'standartuli'; // ვარიანტი
  daaklaki?: () => void;                     // დააკლაკე
  stili?: ViewStyle;                         // სტილი
  gamotvlili?: boolean;                      // გამოთვლილი
  sigrze?: number | string;                  // სიგრძე
  simagre?: number | string;                 // სიმაღლე
}

export const KartKarti: React.FC<KartKartiProps> = ({
  shvilebi,
  varianti = 'standartuli',
  daaklaki,
  stili,
  gamotvlili = false,
  sigrze,
  simagre,
}) => {
  const theme = useKartTheme();

  // ვარიანტის სტილის მიღება
  const getVariantStyle = (): ViewStyle => {
    const variantMap = {
      'naxati': 'artwork',
      'profili': 'profile',
      'standartuli': 'standard',
    } as const;

    const mappedVariant = variantMap[varianti] as keyof typeof theme.components.card;
    const baseStyle = theme.components.card[mappedVariant];

    return {
      ...baseStyle,
      opacity: gamotvlili ? 0.5 : 1,
      ...(sigrze && { width: sigrze }),
      ...(simagre && { height: simagre }),
    };
  };

  const cardStyle = getVariantStyle();
  const Container = daaklaki ? TouchableOpacity : View;

  return (
    <Container
      style={[cardStyle, stili]}
      onPress={daaklaki}
      disabled={gamotvlili}
      activeOpacity={daaklaki ? 0.9 : 1}
    >
      {shvilebi}
    </Container>
  );
};

export default KartKarti;
