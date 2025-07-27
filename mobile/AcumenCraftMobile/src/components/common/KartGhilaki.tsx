import React from 'react';
import {
  TouchableOpacity,
  Text,
  ViewStyle,
  TextStyle,
  ActivityIndicator,
} from 'react-native';
import { useKartTheme } from '../../contexts/KartThemeContext';

interface KartGhilakiProps {
  satauri: string;                           // სათაური
  varianti?: 'dziritadi' | 'meoardi' | 'teqsti'; // ვარიანტი
  zoma?: 'patara' | 'sashualod' | 'didi';   // ზომა
  gamotvlili?: boolean;                      // გამოთვლილი
  mtavaria?: boolean;                        // მთავარია (loading)
  daaklaki?: () => void;                     // დააკლაკე
  stili?: ViewStyle;                         // სტილი
  teqstisStili?: TextStyle;                  // ტექსტის სტილი
  sigrze?: number | string;                  // სიგრძე
}

export const KartGhilaki: React.FC<KartGhilakiProps> = ({
  satauri,
  varianti = 'dziritadi',
  zoma = 'sashualod',
  gamotvlili = false,
  mtavaria = false,
  daaklaki,
  stili,
  teqstisStili,
  sigrze,
}) => {
  const theme = useKartTheme();

  // ზომების კონფიგურაცია
  const getSizeConfig = () => {
    switch (zoma) {
      case 'patara':
        return {
          paddingVertical: theme.spacing.sm,
          paddingHorizontal: theme.spacing.md,
          fontSize: theme.typography.sizes.sm,
        };
      case 'didi':
        return {
          paddingVertical: theme.spacing.lg,
          paddingHorizontal: theme.spacing.xl,
          fontSize: theme.typography.sizes.lg,
        };
      default:
        return {
          paddingVertical: theme.spacing.md,
          paddingHorizontal: theme.spacing.lg,
          fontSize: theme.typography.sizes.base,
        };
    }
  };

  // ვარიანტის სტილი
  const getVariantStyle = (): ViewStyle & TextStyle => {
    const variantMap = {
      'dziritadi': 'primary',
      'meoardi': 'secondary',
      'teqsti': 'text',
    } as const;

    const mappedVariant = variantMap[varianti] as keyof typeof theme.components.button;
    const baseStyle = theme.components.button[mappedVariant];
    const sizeConfig = getSizeConfig();

    return {
      ...baseStyle,
      ...sizeConfig,
      opacity: gamotvlili ? 0.5 : 1,
      width: sigrze,
    };
  };

  const buttonStyle = getVariantStyle();

  return (
    <TouchableOpacity
      style={[buttonStyle, stili]}
      onPress={daaklaki}
      disabled={gamotvlili || mtavaria}
      activeOpacity={0.8}
    >
      {mtavaria ? (
        <ActivityIndicator
          size="small"
          color={
            varianti === 'dziritadi'
              ? theme.colors.gray[900]
              : theme.colors.gold[500]
          }
        />
      ) : (
        <Text
          style={[
            {
              color: buttonStyle.color,
              fontSize: buttonStyle.fontSize,
              fontFamily: buttonStyle.fontFamily || theme.typography.fonts.georgian.body,
              fontWeight: buttonStyle.fontWeight as any,
              textAlign: 'center',
            },
            teqstisStili,
          ]}
        >
          {satauri}
        </Text>
      )}
    </TouchableOpacity>
  );
};

export default KartGhilaki;
