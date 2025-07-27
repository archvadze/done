import React from 'react';
import { Text, TextStyle } from 'react-native';
import { useKartTheme } from '../../contexts/KartThemeContext';

interface KartTeqstiProps {
  shvilebi: React.ReactNode;                 // შვილები (ტექსტი)
  varianti?: 'satauri1' | 'satauri2' | 'satauri3' | 'satauri4' | 'satauri5' | 'satauri6' | 'paragraphi' | 'patara' | 'dzalianPatara'; // ვარიანტი
  feri?: string;                             // ფერი
  wona?: 'msubuqi' | 'normaluri' | 'sashualod' | 'nakhevriadMuki' | 'muki' | 'dzalianMuki'; // წონა
  teqstisGaswporeba?: 'marcxena' | 'centri' | 'marjvena'; // ტექსტის გასწორება
  stili?: TextStyle;                         // სტილი
  raodenobisWyveta?: number;                 // რაოდენობის წყვეტა
}

export const KartTeqsti: React.FC<KartTeqstiProps> = ({
  shvilebi,
  varianti = 'paragraphi',
  feri,
  wona,
  teqstisGaswporeba = 'marcxena',
  stili,
  raodenobisWyveta,
}) => {
  const theme = useKartTheme();

  // ვარიანტის კონფიგურაცია
  const getVariantConfig = () => {
    switch (varianti) {
      case 'satauri1':
        return {
          fontSize: theme.typography.sizes['6xl'],
          fontFamily: theme.typography.fonts.georgian.primary,
          fontWeight: theme.typography.weights.bold,
          color: theme.colors.gold[700],
          lineHeight: theme.typography.lineHeights.tight,
        };
      case 'satauri2':
        return {
          fontSize: theme.typography.sizes['5xl'],
          fontFamily: theme.typography.fonts.georgian.primary,
          fontWeight: theme.typography.weights.bold,
          color: theme.colors.gold[700],
          lineHeight: theme.typography.lineHeights.tight,
        };
      case 'satauri3':
        return {
          fontSize: theme.typography.sizes['4xl'],
          fontFamily: theme.typography.fonts.georgian.primary,
          fontWeight: theme.typography.weights.semibold,
          color: theme.colors.gold[600],
          lineHeight: theme.typography.lineHeights.normal,
        };
      case 'satauri4':
        return {
          fontSize: theme.typography.sizes['3xl'],
          fontFamily: theme.typography.fonts.georgian.primary,
          fontWeight: theme.typography.weights.semibold,
          color: theme.colors.gold[600],
          lineHeight: theme.typography.lineHeights.normal,
        };
      case 'satauri5':
        return {
          fontSize: theme.typography.sizes['2xl'],
          fontFamily: theme.typography.fonts.georgian.body,
          fontWeight: theme.typography.weights.medium,
          color: theme.colors.gold[500],
          lineHeight: theme.typography.lineHeights.normal,
        };
      case 'satauri6':
        return {
          fontSize: theme.typography.sizes.xl,
          fontFamily: theme.typography.fonts.georgian.body,
          fontWeight: theme.typography.weights.medium,
          color: theme.colors.gold[500],
          lineHeight: theme.typography.lineHeights.normal,
        };
      case 'patara':
        return {
          fontSize: theme.typography.sizes.sm,
          fontFamily: theme.typography.fonts.georgian.body,
          fontWeight: theme.typography.weights.normal,
          color: theme.colors.textSecondary,
          lineHeight: theme.typography.lineHeights.normal,
        };
      case 'dzalianPatara':
        return {
          fontSize: theme.typography.sizes.xs,
          fontFamily: theme.typography.fonts.georgian.body,
          fontWeight: theme.typography.weights.normal,
          color: theme.colors.textSecondary,
          lineHeight: theme.typography.lineHeights.normal,
        };
      default: // paragraphi
        return {
          fontSize: theme.typography.sizes.base,
          fontFamily: theme.typography.fonts.georgian.body,
          fontWeight: theme.typography.weights.normal,
          color: theme.colors.text,
          lineHeight: theme.typography.lineHeights.relaxed,
        };
    }
  };

  // წონის მაპინგი
  const getWeightMapping = () => {
    if (!wona) return undefined;

    const weightMap = {
      'msubuqi': theme.typography.weights.light,
      'normaluri': theme.typography.weights.normal,
      'sashualod': theme.typography.weights.medium,
      'nakhevriadMuki': theme.typography.weights.semibold,
      'muki': theme.typography.weights.bold,
      'dzalianMuki': theme.typography.weights.extrabold,
    };

    return weightMap[wona];
  };

  // ტექსტის გასწორების მაპინგი
  const getTextAlign = () => {
    switch (teqstisGaswporeba) {
      case 'centri':
        return 'center';
      case 'marjvena':
        return 'right';
      default:
        return 'left';
    }
  };

  const variantConfig = getVariantConfig();
  const fontWeight = getWeightMapping();
  const textAlign = getTextAlign();

  const finalStyle: TextStyle = {
    ...variantConfig,
    ...(feri && { color: feri }),
    ...(fontWeight && { fontWeight: fontWeight as any }),
    textAlign: textAlign as any,
    ...stili,
  };

  return (
    <Text
      style={finalStyle}
      numberOfLines={raodenobisWyveta}
      ellipsizeMode="tail"
    >
      {shvilebi}
    </Text>
  );
};

export default KartTeqsti;
