set -x
cd obfuscator  && \
rm -fr yakpro-po && \
rm -fr zapier && \
unzip -q ../releases/zapier_perfexcrm_$1.zip && \
yakpro-po --silent --config-file yakpro-po.cnf  zapier -o . > /dev/null 2>&1 && \
cd yakpro-po/ && \
mv obfuscated zapier && \
echo  "\n/*\nModule Name: Zapier\nDescription: Zapier module for Perfex CRM\nVersion: $1\n*/" >>  zapier/zapier.php && \
zip -q -r ../../releases/zapier_perfexcrm_$1_obfuscated.zip zapier && \
cd .. && \
rm -fr yakpro-po && \
rm -fr zapier 
