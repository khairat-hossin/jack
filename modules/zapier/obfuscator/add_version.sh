head -n -6 zapier.php > /tmp/zapier.php && \
cp /tmp/zapier.php zapier.php && \
echo  "\n/*\nModule Name: Zapier\nDescription: Zapier module for Perfex CRM\nVersion: $1\n*/" >>  zapier.php